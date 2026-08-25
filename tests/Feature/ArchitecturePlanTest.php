<?php

namespace Tests\Feature;

use Domain\Novel\Actions\ImportChapterAction;
use Domain\Novel\Models\ChapterSourceVersion;
use Domain\Novel\Models\Novel;
use Domain\Novel\Services\RightsManagementGate;
use Domain\Script\Models\Script;
use Domain\Script\Services\ScriptQualityChecker;
use Domain\Shared\Models\MediaAsset;
use Domain\StoryMemory\Models\ChapterFact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class ArchitecturePlanTest extends TestCase
{
    use RefreshDatabase;

    public function test_chapter_import_records_immutable_source_version(): void
    {
        $novel = Novel::factory()->create(['rights_status' => 'PERMISSION_GRANTED']);

        $action = new ImportChapterAction;
        $chapter = $action->handle($novel, 1, 'Chapter One', 'Raw original content text for chapter one.');

        $this->assertDatabaseHas('chapter_source_versions', [
            'chapter_id' => $chapter->id,
            'version' => 1,
            'content_hash' => hash('sha256', 'Raw original content text for chapter one.'),
        ]);

        $this->assertEquals(1, ChapterSourceVersion::where('chapter_id', $chapter->id)->count());
    }

    public function test_rights_management_gate_blocks_restricted_novels(): void
    {
        $restrictedNovel = Novel::factory()->create(['rights_status' => 'RESTRICTED']);
        $permittedNovel = Novel::factory()->create(['rights_status' => 'PERMISSION_GRANTED']);

        $gate = new RightsManagementGate;

        // Permitted novel should not throw
        $gate->authorize($permittedNovel);

        // Restricted novel must throw RuntimeException
        $this->expectException(RuntimeException::class);
        $gate->authorize($restrictedNovel);
    }

    public function test_script_quality_checker_evaluates_duration_and_warnings(): void
    {
        $novel = Novel::factory()->create();
        $action = new ImportChapterAction;
        $chapter = $action->handle($novel, 1, 'Test Chapter', 'Short text content.');

        ChapterFact::create([
            'chapter_id' => $chapter->id,
            'statement' => 'Protagonist Arin enters the Dark Ruins.',
        ]);

        $script = Script::create([
            'chapter_id' => $chapter->id,
            'version' => 1,
            'full_script' => 'इस कहानी की शुरुआत में आरिन डार्क रुइन्स में प्रवेश करता है।',
            'status' => 'GENERATED',
        ]);

        $checker = new ScriptQualityChecker;
        $result = $checker->check($script, $chapter);

        $this->assertIsArray($result);
        $this->assertGreaterThan(0, $result['estimated_duration_sec']);
    }

    public function test_media_asset_registry_persists_assets(): void
    {
        $novel = Novel::factory()->create();
        $action = new ImportChapterAction;
        $chapter = $action->handle($novel, 1, 'Media Chapter', 'Sample text.');

        $asset = MediaAsset::create([
            'chapter_id' => $chapter->id,
            'type' => 'VIDEO',
            'version' => 1,
            'storage_disk' => 'public',
            'storage_path' => 'novels/test/chapters/1/video/final.mp4',
            'mime_type' => 'video/mp4',
            'size' => 1024000,
            'status' => 'READY',
        ]);

        $this->assertDatabaseHas('media_assets', [
            'id' => $asset->id,
            'type' => 'VIDEO',
            'storage_disk' => 'public',
        ]);
    }
}
