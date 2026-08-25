<?php

namespace Domain\Script\Actions;

use Domain\Script\Data\UpdateScriptData;
use Domain\Script\Models\Script;
use Illuminate\Http\RedirectResponse;
use Lorisleiva\Actions\Concerns\AsAction;

class UpdateHindiScriptAction
{
    use AsAction;

    public function handle(Script $script, UpdateScriptData $data): Script
    {
        $fullScript = implode("\n\n", array_filter([
            $data->hook ?? $script->hook,
            $data->previous_recap ?? $script->previous_recap,
            $data->main_narration ?? $script->main_narration,
            $data->analysis ?? $script->analysis,
            $data->ending_hook ?? $script->ending_hook,
        ]));

        $script->update([
            'hook' => $data->hook ?? $script->hook,
            'previous_recap' => $data->previous_recap ?? $script->previous_recap,
            'main_narration' => $data->main_narration ?? $script->main_narration,
            'analysis' => $data->analysis ?? $script->analysis,
            'ending_hook' => $data->ending_hook ?? $script->ending_hook,
            'full_script' => $fullScript,
            'word_count' => str_word_count($fullScript),
            'character_count' => mb_strlen($fullScript),
        ]);

        return $script;
    }

    public function asController(Script $script, UpdateScriptData $data): RedirectResponse
    {
        $this->handle($script, $data);

        return back()->with('success', 'Script updated successfully.');
    }
}
