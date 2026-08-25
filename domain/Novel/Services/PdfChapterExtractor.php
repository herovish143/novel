<?php

namespace Domain\Novel\Services;

use Exception;
use Smalot\PdfParser\Parser as PdfParser;

class PdfChapterExtractor
{
    public function __construct(
        protected PdfParser $parser = new PdfParser
    ) {}

    /**
     * @return array<int, array{chapter_number: int, title: string, source_text: string, word_count: int}>
     */
    public function extractFromPath(string $filePath): array
    {
        try {
            $pdf = $this->parser->parseFile($filePath);
            $fullText = $pdf->getText();
        } catch (Exception $e) {
            throw new Exception("Failed to parse PDF document: {$e->getMessage()}", 0, $e);
        }

        return $this->extractFromText($fullText);
    }

    /**
     * @return array<int, array{chapter_number: int, title: string, source_text: string, word_count: int}>
     */
    public function extractFromText(string $fullText): array
    {
        $normalized = (string) preg_replace("/\r\n|\r/", "\n", $fullText);
        $normalized = trim($normalized);

        if ($normalized === '') {
            return [];
        }

        // Match delimiters: Chapter 1, CHAPTER II, Ch. 5, अध्याय 1, Episode 3
        $pattern = '/(?=\n(?:Chapter|CHAPTER|Ch\.|Episode|EPISODE|अध्याय)\s*(?:\d+|[IVXLCDM]+))/i';
        $chunks = preg_split($pattern, $normalized, -1, PREG_SPLIT_NO_EMPTY);

        if (! $chunks || count($chunks) === 0) {
            $chunks = [$normalized];
        }

        $extracted = [];
        $chapterCount = 1;

        foreach ($chunks as $chunk) {
            $chunk = trim($chunk);
            if ($chunk === '' || str_word_count($chunk) < 2) {
                continue;
            }

            $lines = explode("\n", $chunk);
            $title = trim($lines[0]);

            if (strlen($title) > 80) {
                $title = "Chapter {$chapterCount}: ".substr($title, 0, 60).'...';
            }

            $extracted[] = [
                'chapter_number' => $chapterCount,
                'title' => $title,
                'source_text' => $chunk,
                'word_count' => str_word_count($chunk),
            ];

            $chapterCount++;
        }

        return $extracted;
    }
}
