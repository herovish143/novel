<?php

use Domain\Billing\Actions\CostsDashboardAction;
use Domain\DocumentImport\Actions\ApproveChapterCandidateAction;
use Domain\DocumentImport\Actions\CreatePdfImportAction;
use Domain\DocumentImport\Actions\ImportApprovedChaptersAction;
use Domain\DocumentImport\Actions\MergeChapterCandidatesAction;
use Domain\DocumentImport\Actions\ShowDocumentImportAction;
use Domain\DocumentImport\Actions\SkipChapterCandidateAction;
use Domain\DocumentImport\Actions\SplitChapterCandidateAction;
use Domain\DocumentImport\Actions\UpdateChapterCandidateAction;
use Domain\Novel\Actions\AnalyzeChapterAction;
use Domain\Novel\Actions\CreateNovelAction;
use Domain\Novel\Actions\ExtractPdfChaptersAction;
use Domain\Novel\Actions\ImportChapterAction;
use Domain\Novel\Actions\ListNovelsAction;
use Domain\Novel\Actions\ShowChapterAction;
use Domain\Novel\Actions\ShowNovelAction;
use Domain\Production\Actions\PipelineMonitorAction;
use Domain\Production\Actions\ResumeProductionAction;
use Domain\Production\Actions\StartProductionAction;
use Domain\Publishing\Actions\PublishYouTubeVideoAction;
use Domain\Publishing\Actions\ReviewQueueAction;
use Domain\Script\Actions\ApproveHindiScriptAction;
use Domain\Script\Actions\GenerateHindiScriptAction;
use Domain\Script\Actions\ReviewHindiScriptAction;
use Domain\Script\Actions\UpdateHindiScriptAction;
use Domain\Script\Actions\VerifyHindiScriptAction;
use Domain\Video\Actions\RenderVideoAction;
use Domain\Visual\Actions\PlanScenesAction;
use Domain\Visual\Actions\RegenerateSceneAction;
use Domain\Visual\Actions\ShowSceneEditorAction;
use Domain\Voice\Actions\GenerateNarrationAction;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function (): void {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    // Operations & Workspace Routes
    Route::get('/reviews', ReviewQueueAction::class)->name('reviews.index');
    Route::get('/pipeline', PipelineMonitorAction::class)->name('pipeline.index');
    Route::get('/costs', CostsDashboardAction::class)->name('costs.index');
    Route::get('/episodes/{chapter}', ShowChapterAction::class)->name('episodes.show');

    // Novel Automation Domain Routes (Laravel Actions)
    Route::get('/novels', ListNovelsAction::class)->name('novels.index');
    Route::post('/novels', CreateNovelAction::class)->name('novels.store');
    Route::get('/novels/{novel}', ShowNovelAction::class)->name('novels.show');
    Route::post('/novels/{novel}/pdf/import', ExtractPdfChaptersAction::class)->name('novels.pdf.import');
    Route::post('/novels/{novel}/pdf/upload', CreatePdfImportAction::class)->name('novels.pdf.upload');

    // Document Import Candidate Review Routes
    Route::get('/document-imports/{documentImport}', ShowDocumentImportAction::class)->name('document-imports.show');
    Route::post('/document-imports/{documentImport}/import', ImportApprovedChaptersAction::class)->name('document-imports.import');
    Route::patch('/chapter-candidates/{candidate}', UpdateChapterCandidateAction::class)->name('chapter-candidates.update');
    Route::post('/chapter-candidates/{candidate}/approve', ApproveChapterCandidateAction::class)->name('chapter-candidates.approve');
    Route::post('/chapter-candidates/{candidate}/skip', SkipChapterCandidateAction::class)->name('chapter-candidates.skip');
    Route::post('/chapter-candidates/{candidate}/split', SplitChapterCandidateAction::class)->name('chapter-candidates.split');
    Route::post('/chapter-candidates/{candidate}/merge-next', MergeChapterCandidatesAction::class)->name('chapter-candidates.merge-next');

    Route::post('/novels/{novel}/chapters', ImportChapterAction::class)->name('chapters.store');
    Route::get('/chapters/{chapter}', ShowChapterAction::class)->name('chapters.show');
    Route::post('/chapters/{chapter}/analyze', AnalyzeChapterAction::class)->name('chapters.analyze');

    Route::post('/chapters/{chapter}/script/generate', GenerateHindiScriptAction::class)->name('scripts.generate');
    Route::get('/scripts/{script}/review', ReviewHindiScriptAction::class)->name('scripts.review');
    Route::post('/scripts/{script}/verify', VerifyHindiScriptAction::class)->name('scripts.verify');
    Route::put('/scripts/{script}', UpdateHindiScriptAction::class)->name('scripts.update');
    Route::post('/scripts/{script}/approve', ApproveHindiScriptAction::class)->name('scripts.approve');

    // Phase 2: Voice Narration Route
    Route::post('/scripts/{script}/audio/generate', GenerateNarrationAction::class)->name('scripts.audio.generate');

    // Phase 3: Visual Engine Routes
    Route::post('/chapters/{chapter}/scenes/plan', PlanScenesAction::class)->name('scenes.plan');
    Route::get('/chapters/{chapter}/scenes/editor', ShowSceneEditorAction::class)->name('scenes.editor');
    Route::post('/scenes/{scene}/regenerate', RegenerateSceneAction::class)->name('scenes.regenerate');

    // Phase 4: Video Renderer Route
    Route::post('/chapters/{chapter}/video/render', RenderVideoAction::class)->name('video.render');

    // Phase 5: Production Automation Routes
    Route::post('/chapters/{chapter}/production/start', StartProductionAction::class)->name('production.start');
    Route::post('/chapters/{chapter}/production/resume', ResumeProductionAction::class)->name('production.resume');

    // Phase 6: YouTube Publishing Route
    Route::post('/chapters/{chapter}/youtube/publish', PublishYouTubeVideoAction::class)->name('youtube.publish');
});

require __DIR__.'/settings.php';
