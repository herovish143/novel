<?php

namespace Domain\Novel\Services;

use Domain\Novel\Models\Novel;
use RuntimeException;

class RightsManagementGate
{
    /**
     * Verify that novel copyright and rights status permits export / publishing.
     */
    public function authorize(Novel $novel): void
    {
        $status = strtoupper($novel->rights_status ?? 'PERMISSION_GRANTED');

        if (in_array($status, ['UNKNOWN', 'RESTRICTED'], true)) {
            throw new RuntimeException("Export blocked by Rights Management Gate: Novel rights status is '{$status}'. Admin authorization required.");
        }
    }
}
