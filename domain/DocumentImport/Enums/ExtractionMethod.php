<?php

namespace Domain\DocumentImport\Enums;

enum ExtractionMethod: string
{
    case NATIVE = 'NATIVE';
    case OCR = 'OCR';
    case NATIVE_WITH_OCR_FALLBACK = 'NATIVE_WITH_OCR_FALLBACK';
}
