<?php

namespace Domain\Script\Actions;

use Domain\Script\Models\Script;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Lorisleiva\Actions\Concerns\AsAction;

class ApproveHindiScriptAction
{
    use AsAction;

    public function handle(Script $script, ?int $userId = null): Script
    {
        $script->update([
            'status' => 'APPROVED',
            'approved_at' => now(),
            'approved_by' => $userId,
        ]);

        $script->chapter->update(['status' => 'SCRIPT_APPROVED']);

        return $script;
    }

    public function asController(Request $request, Script $script): RedirectResponse
    {
        $this->handle($script, $request->user()?->id);

        return to_route('chapters.show', $script->chapter_id)->with('success', 'Script approved for narration.');
    }
}
