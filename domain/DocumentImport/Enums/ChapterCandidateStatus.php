<?php

namespace Domain\DocumentImport\Enums;

enum ChapterCandidateStatus: string
{
    case DETECTED = 'DETECTED';
    case REVIEW_REQUIRED = 'REVIEW_REQUIRED';
    case APPROVED = 'APPROVED';
    case SKIPPED = 'SKIPPED';
    case IMPORTED = 'IMPORTED';
    case DUPLICATE = 'DUPLICATE';
    case FAILED = 'FAILED';
}
