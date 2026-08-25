<?php

namespace Domain\Voice\Services;

use Domain\Novel\Models\Chapter;
use Domain\Script\Models\Script;

class SubtitleGenerator
{
    /**
     * Generate SRT and ASS subtitle files from script segments and audio durations.
     *
     * @param  array<int, array{text: string, start_ms: int, end_ms: int}>  $timedSegments
     * @return array{srt: string, ass: string}
     */
    public function generate(Chapter $chapter, Script $script, array $timedSegments): array
    {
        $srt = $this->buildSrt($timedSegments);
        $ass = $this->buildAss($chapter, $timedSegments);

        return [
            'srt' => $srt,
            'ass' => $ass,
        ];
    }

    /**
     * @param  array<int, array{text: string, start_ms: int, end_ms: int}>  $segments
     */
    protected function buildSrt(array $segments): string
    {
        $lines = [];
        $index = 1;

        foreach ($segments as $seg) {
            $start = $this->formatSrtTime($seg['start_ms']);
            $end = $this->formatSrtTime($seg['end_ms']);
            $text = trim($seg['text']);

            $lines[] = "{$index}\n{$start} --> {$end}\n{$text}\n";
            $index++;
        }

        return implode("\n", $lines);
    }

    /**
     * @param  array<int, array{text: string, start_ms: int, end_ms: int}>  $segments
     */
    protected function buildAss(Chapter $chapter, array $segments): string
    {
        $title = "Ch. {$chapter->chapter_number} - {$chapter->title}";

        $header = <<<ASS
[Script Info]
Title: {$title}
ScriptType: v4.00+
WrapStyle: 0
ScaledBorderAndShadow: yes
YCbCr Matrix: None
PlayResX: 1920
PlayResY: 1080

[V4+ Styles]
Format: Name, Fontname, Fontsize, PrimaryColour, SecondaryColour, OutlineColour, BackColour, Bold, Italic, Underline, StrikeOut, ScaleX, ScaleY, Spacing, Angle, BorderStyle, Outline, Shadow, Alignment, MarginL, MarginR, MarginV, Encoding
Style: Default,Arial,48,&H00FFFFFF,&H000000FF,&H00000000,&H80000000,-1,0,0,0,100,100,0,0,1,3,2,2,40,40,60,1

[Events]
Format: Layer, Start, End, Style, Name, MarginL, MarginR, MarginV, Effect, Text
ASS;

        $eventLines = [];
        foreach ($segments as $seg) {
            $start = $this->formatAssTime($seg['start_ms']);
            $end = $this->formatAssTime($seg['end_ms']);
            $text = str_replace("\n", '\\N', trim($seg['text']));

            $eventLines[] = "Dialogue: 0,{$start},{$end},Default,,0,0,0,,{$text}";
        }

        return $header."\n".implode("\n", $eventLines)."\n";
    }

    protected function formatSrtTime(int $milliseconds): string
    {
        $seconds = (int) floor($milliseconds / 1000);
        $ms = $milliseconds % 1000;
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%02d:%02d:%02d,%03d', $hours, $minutes, $secs, $ms);
    }

    protected function formatAssTime(int $milliseconds): string
    {
        $seconds = (int) floor($milliseconds / 1000);
        $cs = (int) floor(($milliseconds % 1000) / 10);
        $hours = (int) floor($seconds / 3600);
        $minutes = (int) floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        return sprintf('%d:%02d:%02d.%02d', $hours, $minutes, $secs, $cs);
    }
}
