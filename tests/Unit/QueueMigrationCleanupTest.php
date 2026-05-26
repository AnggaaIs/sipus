<?php

test('jobs migration file has been removed', function () {
    $migrationPath = dirname(__DIR__, 2).DIRECTORY_SEPARATOR.'database'
        .DIRECTORY_SEPARATOR.'migrations'
        .DIRECTORY_SEPARATOR.'0001_01_01_000002_create_jobs_table.php';

    expect($migrationPath)->not->toBeFile();
});
