<?php

declare(strict_types=1);

require_once __DIR__.'/get-changed-files.php';

$files = getChangedPhpFiles();

if ($files === []) {
    echo "No changed PHP files detected.\n";
    exit(0);
}

echo 'Running PHPStan analysis on '.count($files)." changed file(s):\n";
foreach ($files as $file) {
    echo ' - '.$file."\n";
}
echo "\n";

$extraArgs = array_slice($argv, 1);
$cmd = array_merge(
    [PHP_BINARY, __DIR__.'/../vendor/bin/phpstan', 'analyse'],
    $extraArgs,
    $files
);

$escaped = array_map('escapeshellarg', $cmd);
$commandLine = implode(' ', $escaped);

passthru($commandLine, $exitCode);
exit($exitCode);
