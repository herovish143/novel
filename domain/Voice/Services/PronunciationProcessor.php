<?php

namespace Domain\Voice\Services;

use Domain\Novel\Models\Novel;

class PronunciationProcessor
{
    /**
     * Process raw Hindi script text with novel pronunciation dictionary overrides.
     */
    public function process(Novel $novel, string $text): string
    {
        $pronunciations = $novel->pronunciations;

        if ($pronunciations->isEmpty()) {
            return $text;
        }

        $processed = $text;
        foreach ($pronunciations as $rule) {
            if ($rule->term !== '' && $rule->pronunciation !== '') {
                $processed = str_replace($rule->term, $rule->pronunciation, $processed);
            }
        }

        return $processed;
    }
}
