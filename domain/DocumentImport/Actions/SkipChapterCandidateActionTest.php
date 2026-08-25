<?php

namespace Domain\DocumentImport\Actions;

use App\Models\User;
use Domain\DocumentImport\Enums\ChapterCandidateStatus;
use Domain\DocumentImport\Models\ChapterCandidate;

test('skip chapter candidate action updates status to SKIPPED', function () {
    $user = User::factory()->create();
    $candidate = ChapterCandidate::factory()->create();

    $response = $this->actingAs($user)->post(route('chapter-candidates.skip', $candidate->id));

    expect($candidate->fresh()->status)->toBe(ChapterCandidateStatus::SKIPPED);
});
