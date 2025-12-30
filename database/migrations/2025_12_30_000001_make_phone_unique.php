<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `users` ADD UNIQUE (`phone`)');
        } elseif ($driver === 'pgsql') {
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_phone_unique ON users (phone)');
        } elseif ($driver === 'sqlite') {
            // SQLite: unique index creation works if column exists
            DB::statement('CREATE UNIQUE INDEX IF NOT EXISTS users_phone_unique ON users (phone)');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE `users` DROP INDEX `phone`');
        } elseif ($driver === 'pgsql') {
            DB::statement('DROP INDEX IF EXISTS users_phone_unique');
        } elseif ($driver === 'sqlite') {
            DB::statement('DROP INDEX IF EXISTS users_phone_unique');
        }
    }
};
