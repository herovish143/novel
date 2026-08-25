<?php

namespace Domain\DocumentImport\Enums;

enum DocumentImportStatus: string
{
    case UPLOADED = 'UPLOADED';
    case EXTRACTING = 'EXTRACTING';
    case DETECTING = 'DETECTING';
    case REVIEW_REQUIRED = 'REVIEW_REQUIRED';
    case IMPORTING = 'IMPORTING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
}
