<?php

$paths = ['src', 'tests'];
$last = 0;

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
        passthru("php vendor/bin/phpunit --testdox tests");
    }
    usleep(300000); // 300ms
}
