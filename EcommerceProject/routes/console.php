<?php

use App\Console\Commands\CleanupCartsCommand;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

Artisan::command('app:refresh', function () {
    // Drop all database tables while ignoring foreign key constraints
    Schema::withoutForeignKeyConstraints(function () {
        Schema::dropAllTables();
    });

    // Clean up all directories in public storage
    $directories = Storage::disk('public')->directories();
    !empty($directories) && array_walk($directories, fn($directory) => Storage::disk('public')->deleteDirectory($directory));

    // Drop all stored procedures and functions
    DB::unprepared(<<<SQL
        DROP PROCEDURE IF EXISTS refresh_and_cleanup_carts
    SQL);

})->purpose('Reset application by dropping all database tables, cleaning storage, and removing stored procedures');

Schedule::command(CleanupCartsCommand::class)
    ->hourly()
    ->withoutOverlapping()
    ->onOneServer();
