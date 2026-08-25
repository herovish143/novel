<?php

declare(strict_types=1);

/**
 * Returns an array of changed PHP file paths.
 * Checks git modified, added, staged, and untracked PHP files.
 *
 * @param  array<int, string>  $allowedPrefixes
 * @return list<string>
 */
function getChangedPhpFiles(array $allowedPrefixes = []): array
{
    $commands = [
        'git diff --name-only --diff-filter=ACMR HEAD',
        'git diff --name-only --diff-filter=ACMR',
        'git ls-files --others --exclude-standard',
    ];

    $files = [];

    foreach ($commands as $cmd) {
        $output = shell_exec($cmd);
        if (is_string($output) && trim($output) !== '') {
            $lines = explode("\n", trim($output));
            foreach ($lines as $line) {
                $file = trim($line);
                if ($file !== '' && str_ends_with($file, '.php') && file_exists($file)) {
                    if (str_starts_with($file, 'vendor/') ||
                        str_starts_with($file, 'storage/') ||
                        str_starts_with($file, 'node_modules/') ||
                        str_starts_with($file, 'public/')) {
                        continue;
                    }

                    if ($allowedPrefixes !== []) {
                        $matches = false;
                        foreach ($allowedPrefixes as $prefix) {
                            if (str_starts_with($file, $prefix)) {
                                $matches = true;
                                break;
                            }
                        }
                        if (! $matches) {
                            continue;
                        }
                    }

                    $files[$file] = $file;
                }
            }
        }
    }

    return array_values($files);
}
