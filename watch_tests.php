<?php

$paths = ['src', 'tests'];
$last = 0;

// Parse optional --filter argument
$filter = null;
foreach ($argv as $arg) {
    if (str_starts_with($arg, '--filter=')) {
        $filter = substr($arg, strlen('--filter='));
    }
}

function latestMTime(array $paths_array) {
    $latest = 0;
    foreach ($paths_array as $path) {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $file) {
            $mtime = $file->getMTime();
            if ($mtime > $latest) {
                $latest = $mtime;
            }
        }
    }
    return $latest;
}

echo "Watching for changes...\n";

while (true) {
    $current = latestMTime($paths);
    if ($current > $last) {
        $last = $current;
        echo "\nChange detected — running tests...\n";

        $cmd = "php vendor/bin/phpunit --testdox tests";

        if ($filter !== null) {
            $cmd .= " --filter=" . escapeshellarg($filter);
        }

        passthru($cmd);
    }
    usleep(300000); // 300ms
}
