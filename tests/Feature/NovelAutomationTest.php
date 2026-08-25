<?php

use App\Models\User;
use Domain\Billing\Services\EpisodeBudgetGuard;
use Domain\Novel\Actions\AnalyzeChapterAction;
use Domain\Novel\Actions\ImportChapterAction;
use Domain\Novel\Models\Chapter;
use Domain\Novel\Models\Novel;
use Domain\Production\Services\ProductionOrchestrator;
use Domain\Production\Services\ScheduledChapterChecker;
use Domain\Publishing\Actions\PublishYouTubeVideoAction;
use Domain\Publishing\Jobs\AutoPublishSchedulerJob;
use Domain\Publishing\Models\YouTubePublication;
use Domain\Publishing\Services\FakeYouTubeService;
use Domain\Publishing\Services\ThumbnailPipeline;
use Domain\Publishing\Services\YouTubeMetadataGenerator;
use Domain\Script\Actions\GenerateHindiScriptAction;
use Domain\Script\Actions\VerifyHindiScriptAction;
use Domain\Script\Models\Script;
use Domain\Shared\Services\Ai\FakeLanguageModel;
use Domain\StoryMemory\Actions\UpdateStoryMemoryAction;
use Domain\StoryMemory\Services\StoryContextBuilder;
use Domain\Video\Actions\RenderVideoAction;
use Domain\Video\Services\FfmpegRenderer;
use Domain\Video\Services\TimelineBuilder;
use Domain\Visual\Actions\PlanScenesAction;
use Domain\Visual\Services\AssetReuseEngine;
use Domain\Visual\Services\FakeImageGenerator;
use Domain\Visual\Services\ScenePlanner;
use Domain\Visual\Services\ScenePromptBuilder;
use Domain\Voice\Actions\GenerateNarrationAction;
use Domain\Voice\Services\FakeSpeechGenerator;
use Domain\Voice\Services\PronunciationProcessor;
use Domain\Voice\Services\SubtitleGenerator;
use Illuminate\Support\Facades\Storage;

test('guests are redirected from novel routes', function (): void {
    $this->get(route('novels.index'))->assertRedirect(route('login'));
});

test('authenticated user can view novel list and create novel', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get(route('novels.index'));
    $response->assertOk();

    $novelData = [
        'title' => 'Shadow Slave Test',
        'original_language' => 'en',
        'output_language' => 'hi',
        'visual_style' => 'dark fantasy',
        'narration_style' => 'conversational Hindi',
        'max_cost_per_episode' => 5.00,
    ];

    $this->post(route('novels.store'), $novelData)
        ->assertRedirect(route('novels.index'));

    $this->assertDatabaseHas('novels', ['title' => 'Shadow Slave Test']);
});

test('import chapter action normalizes text and creates production run', function (): void {
    $novel = Novel::factory()->create();
    $action = new ImportChapterAction;

    $rawText = "<h1>Chapter 1</h1><p>Sunny woke up in a cold dark cave.</p>\n\n\n<script>alert(1)</script>";

    $chapter = $action->handle(
        novel: $novel,
        chapterNumber: 1,
        title: 'The Awakening',
        sourceText: $rawText
    );

    expect($chapter->source_text)->not->toContain('<h1>')
        ->and($chapter->source_text)->not->toContain('<script>')
        ->and($chapter->source_hash)->toBe(hash('sha256', $chapter->source_text))
        ->and($chapter->status)->toBe('IMPORTED');

    $this->assertDatabaseHas('production_runs', [
        'chapter_id' => $chapter->id,
        'status' => 'IMPORTED',
    ]);
});

test('fact extraction and story memory update persists characters, locations, events', function (): void {
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);

    $fakeFacts = [
        'summary' => 'Sunny enters the forgotten temple and discovers a shadow beast.',
        'characters' => [
            [
                'name' => 'Sunny',
                'canonical_name' => 'Sunless',
                'gender' => 'male',
                'physical_description' => 'Slim young man with dark hair.',
                'personality' => 'Cynical and cautious.',
                'importance' => 'MAIN',
            ],
        ],
        'locations' => [
            [
                'name' => 'Forgotten Temple',
                'description' => 'Ancient ruin in the nightmare realm.',
            ],
        ],
        'events' => [
            [
                'sequence' => 1,
                'event_type' => 'PLOT',
                'description' => 'Sunny enters the temple.',
                'importance_score' => 8,
            ],
        ],
        'abilities' => [],
        'items' => [],
        'relationships_changed' => [],
        'important_reveals' => ['Sunny possesses a rare divine aspect.'],
        'unresolved_questions' => ['Who built the ruined temple?'],
    ];

    $fakeAi = new FakeLanguageModel(stubbedStructured: $fakeFacts);
    $analyzeAction = new AnalyzeChapterAction($fakeAi);
    $memoryAction = new UpdateStoryMemoryAction;

    $extractedFacts = $analyzeAction->handle($chapter);
    expect($extractedFacts['summary'])->toBe('Sunny enters the forgotten temple and discovers a shadow beast.');

    $memoryAction->handle($chapter, $extractedFacts);

    $this->assertDatabaseHas('chapter_summaries', [
        'chapter_id' => $chapter->id,
        'summary' => 'Sunny enters the forgotten temple and discovers a shadow beast.',
    ]);

    $this->assertDatabaseHas('characters', [
        'novel_id' => $novel->id,
        'canonical_name' => 'Sunless',
    ]);

    $this->assertDatabaseHas('character_aliases', [
        'alias' => 'Sunny',
    ]);

    $this->assertDatabaseHas('locations', [
        'novel_id' => $novel->id,
        'name' => 'Forgotten Temple',
    ]);
});

test('generate hindi script action creates script and segments', function (): void {
    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);

    $scriptResponse = [
        'hook' => 'नमस्ते दोस्तों, आज हम एक नए एडवेंचर की शुरुआत करेंगे।',
        'previous_recap' => 'पिछले भाग में कोई जानकारी नहीं थी।',
        'main_narration' => 'Sunny एक अंधेरी गुफा में जागा और उसने महसूस किया कि वह अकेला है।',
        'analysis' => 'यह अध्याय Sunny के अस्तित्व के संघर्ष को दिखाता है।',
        'ending_hook' => 'क्या Sunny गुफा से बाहर निकल पाएगा? जानिए अगले एपिसोड में।',
    ];

    $fakeAi = new FakeLanguageModel(stubbedStructured: $scriptResponse);
    $contextBuilder = new StoryContextBuilder;
    $scriptAction = new GenerateHindiScriptAction($fakeAi, $contextBuilder);

    $script = $scriptAction->handle($chapter);

    expect($script->hook)->toContain('नमस्ते')
        ->and($script->status)->toBe('NEEDS_REVIEW')
        ->and($chapter->fresh()->status)->toBe('SCRIPT_REVIEW');

    $this->assertDatabaseHas('script_segments', [
        'script_id' => $script->id,
        'type' => 'HOOK',
    ]);
});

test('script verification checks for factual issues and approval updates status', function (): void {
    $user = User::factory()->create();
    $this->actingAs($user);

    $novel = Novel::factory()->create();
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);
    $script = Script::create([
        'chapter_id' => $chapter->id,
        'version' => 1,
        'language' => 'hi',
        'status' => 'NEEDS_REVIEW',
        'hook' => 'Hook text',
        'main_narration' => 'Narration text',
        'full_script' => 'Hook text\n\nNarration text',
    ]);

    $fakeAi = new FakeLanguageModel(stubbedStructured: [
        'valid' => true,
        'issues' => [],
    ]);

    $verifier = new VerifyHindiScriptAction($fakeAi);
    $result = $verifier->handle($script, ['summary' => 'Fact summary', 'events' => []]);

    expect($result['valid'])->toBeTrue();

    $this->post(route('scripts.approve', $script->id))
        ->assertRedirect(route('chapters.show', $chapter->id));

    expect($script->fresh()->status)->toBe('APPROVED')
        ->and($chapter->fresh()->status)->toBe('SCRIPT_APPROVED');
});

test('generate narration action produces audio segments, master narration, and subtitles', function (): void {
    Storage::fake('public');

    $novel = Novel::factory()->create(['slug' => 'shadow-slave']);
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);
    $script = Script::create([
        'chapter_id' => $chapter->id,
        'version' => 1,
        'language' => 'hi',
        'status' => 'APPROVED',
        'hook' => 'नमस्ते दोस्तों',
        'main_narration' => 'Sunny एक अंधेरी गुफा में जागा।',
        'full_script' => 'नमस्ते दोस्तों\n\nSunny एक अंधेरी गुफा में जागा।',
    ]);

    $script->segments()->create([
        'sequence' => 1,
        'type' => 'HOOK',
        'text' => 'नमस्ते दोस्तों',
        'estimated_duration' => 2.5,
        'status' => 'PENDING',
    ]);

    $fakeTts = new FakeSpeechGenerator;
    $pronunciationProcessor = new PronunciationProcessor;
    $subtitleGenerator = new SubtitleGenerator;

    $action = new GenerateNarrationAction;
    $action->handle($script, $fakeTts, $pronunciationProcessor, $subtitleGenerator);

    $this->assertDatabaseHas('audio_segments', [
        'provider' => 'FakeElevenLabs',
    ]);

    $this->assertDatabaseHas('subtitle_files', [
        'chapter_id' => $chapter->id,
        'format' => 'SRT',
    ]);

    $this->assertDatabaseHas('subtitle_files', [
        'chapter_id' => $chapter->id,
        'format' => 'ASS',
    ]);

    Storage::disk('public')->assertExists("novels/{$novel->slug}/chapters/1/audio/narration.mp3");
    Storage::disk('public')->assertExists("novels/{$novel->slug}/chapters/1/subtitles/episode.srt");
    Storage::disk('public')->assertExists("novels/{$novel->slug}/chapters/1/subtitles/episode.ass");

    expect($chapter->fresh()->status)->toBe('AUDIO_GENERATED');
});

test('plan scenes action creates scene timeline and visual assets', function (): void {
    Storage::fake('public');

    $novel = Novel::factory()->create(['slug' => 'shadow-slave']);
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);
    $script = Script::create([
        'chapter_id' => $chapter->id,
        'version' => 1,
        'language' => 'hi',
        'status' => 'APPROVED',
        'hook' => 'Hook',
        'main_narration' => 'Main narration text',
        'full_script' => 'Hook\n\nMain narration text',
    ]);

    $script->segments()->create([
        'sequence' => 1,
        'type' => 'HOOK',
        'text' => 'Hook segment',
        'estimated_duration' => 15.0,
        'status' => 'COMPLETED',
    ]);

    $fakeAi = new FakeLanguageModel;
    $promptBuilder = new ScenePromptBuilder;
    $reuseEngine = new AssetReuseEngine;
    $planner = new ScenePlanner($fakeAi, $promptBuilder, $reuseEngine);
    $fakeImageGen = new FakeImageGenerator;

    $action = new PlanScenesAction;
    $action->handle($chapter, $planner, $fakeImageGen, $reuseEngine);

    $this->assertDatabaseHas('scenes', [
        'chapter_id' => $chapter->id,
        'sequence' => 1,
    ]);

    $this->assertDatabaseHas('scene_assets', [
        'provider' => 'FakeOpenAI',
    ]);

    expect($chapter->fresh()->status)->toBe('IMAGES_GENERATED');
});

test('render video action builds project, timeline items, and outputs final mp4', function (): void {
    Storage::fake('public');

    $novel = Novel::factory()->create(['slug' => 'shadow-slave']);
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);
    $script = Script::create([
        'chapter_id' => $chapter->id,
        'version' => 1,
        'language' => 'hi',
        'status' => 'APPROVED',
        'hook' => 'Hook',
        'main_narration' => 'Main narration',
        'full_script' => 'Full script',
    ]);

    $scene = $chapter->scenes()->create([
        'script_id' => $script->id,
        'sequence' => 1,
        'start_ms' => 0,
        'end_ms' => 10000,
        'description' => 'Scene 1',
        'image_prompt' => 'Prompt 1',
        'camera_motion' => 'slow_zoom',
        'status' => 'COMPLETED',
    ]);

    $scene->assets()->create([
        'asset_type' => 'IMAGE',
        'provider' => 'FakeOpenAI',
        'prompt' => 'Prompt 1',
        'storage_path' => "novels/{$novel->slug}/chapters/1/scenes/scene_1.webp",
        'width' => 1792,
        'height' => 1024,
        'cost' => 0.04,
        'status' => 'READY',
    ]);

    Storage::disk('public')->put("novels/{$novel->slug}/chapters/1/scenes/scene_1.webp", 'DUMMY_IMAGE');

    $timelineBuilder = new TimelineBuilder;
    $renderer = new FfmpegRenderer;

    $action = new RenderVideoAction;
    $action->handle($chapter, $timelineBuilder, $renderer);

    $this->assertDatabaseHas('video_projects', [
        'chapter_id' => $chapter->id,
        'status' => 'COMPLETED',
    ]);

    $this->assertDatabaseHas('video_timeline_items', [
        'sequence' => 1,
    ]);

    Storage::disk('public')->assertExists("novels/{$novel->slug}/chapters/1/video/final.mp4");

    expect($chapter->fresh()->status)->toBe('VIDEO_RENDERED');
});

test('production orchestrator runs pipeline and enforces budget guard', function (): void {
    Storage::fake('public');

    $novel = Novel::factory()->create(['slug' => 'shadow-slave', 'max_cost_per_episode' => 10.00]);
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);

    $fakeFacts = [
        'summary' => 'Sunny enters the forgotten temple.',
        'characters' => [],
        'locations' => [],
        'events' => [],
        'abilities' => [],
        'items' => [],
        'relationships_changed' => [],
        'important_reveals' => [],
        'unresolved_questions' => [],
    ];

    $fakeAi = new FakeLanguageModel(stubbedStructured: $fakeFacts);
    $budgetGuard = new EpisodeBudgetGuard;
    $analyzeAction = new AnalyzeChapterAction($fakeAi);
    $memoryAction = new UpdateStoryMemoryAction;
    $contextBuilder = new StoryContextBuilder;
    $scriptAction = new GenerateHindiScriptAction($fakeAi, $contextBuilder);
    $narrationAction = new GenerateNarrationAction;
    $scenesAction = new PlanScenesAction;
    $renderAction = new RenderVideoAction;

    $orchestrator = new ProductionOrchestrator(
        $budgetGuard,
        $analyzeAction,
        $memoryAction,
        $scriptAction,
        $narrationAction,
        $scenesAction,
        $renderAction
    );

    $run = $orchestrator->run($chapter);

    expect($run->status)->toBe('WAITING_FOR_APPROVAL')
        ->and($run->current_stage)->toBe('SCRIPT_APPROVAL');

    $this->assertDatabaseHas('production_runs', [
        'chapter_id' => $chapter->id,
        'status' => 'WAITING_FOR_APPROVAL',
    ]);
});

test('publish youtube video action creates publication record and thumbnail', function (): void {
    Storage::fake('public');

    $novel = Novel::factory()->create(['slug' => 'shadow-slave']);
    $chapter = Chapter::factory()->create(['novel_id' => $novel->id, 'chapter_number' => 1]);

    $fakeMeta = [
        'title' => 'Shadow Slave Chapter 1 Hindi Explanation',
        'description' => 'Detailed Hindi explanation of chapter 1.',
        'tags' => ['shadow slave', 'hindi recap'],
    ];

    $fakeAi = new FakeLanguageModel(stubbedStructured: $fakeMeta);
    $fakeImageGen = new FakeImageGenerator;
    $metaGen = new YouTubeMetadataGenerator($fakeAi);
    $thumbPipeline = new ThumbnailPipeline($fakeImageGen);
    $fakeYtService = new FakeYouTubeService;

    $action = new PublishYouTubeVideoAction;
    $publication = $action->handle($chapter, $metaGen, $thumbPipeline, $fakeYtService);

    expect($publication->title)->toBe('Shadow Slave Chapter 1 Hindi Explanation')
        ->and($publication->visibility)->toBe('UNLISTED')
        ->and($chapter->fresh()->status)->toBe('PUBLISHED');

    $this->assertDatabaseHas('youtube_publications', [
        'chapter_id' => $chapter->id,
        'visibility' => 'UNLISTED',
    ]);

    Storage::disk('public')->assertExists("novels/{$novel->slug}/chapters/1/thumbnails/thumbnail.webp");
});

test('scheduled chapter checker imports new chapters and console command runs', function (): void {
    $novel = Novel::factory()->create([
        'source_url' => 'https://example.com/shadow-slave',
        'status' => 'ACTIVE',
    ]);

    $importAction = new ImportChapterAction;
    $checker = new ScheduledChapterChecker($importAction);

    $imported = $checker->checkAll();

    expect($imported)->toBe(1);

    $this->assertDatabaseHas('chapters', [
        'novel_id' => $novel->id,
        'chapter_number' => 1,
    ]);

    $this->artisan('novel:check-chapters')->assertExitCode(0);
});

test('auto publish scheduler job updates unlisted publications to public', function (): void {
    $chapter = Chapter::factory()->create();
    $pub = YouTubePublication::create([
        'chapter_id' => $chapter->id,
        'title' => 'Test Video',
        'description' => 'Test desc',
        'visibility' => 'UNLISTED',
        'publish_status' => 'UPLOADED',
    ]);

    (new AutoPublishSchedulerJob)->handle();

    expect($pub->fresh()->visibility)->toBe('PUBLIC')
        ->and($pub->fresh()->publish_status)->toBe('PUBLISHED')
        ->and($chapter->fresh()->status)->toBe('PUBLISHED');
});
