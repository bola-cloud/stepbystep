<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('church')->nullable()->after('name');
            $table->string('school_year')->nullable()->after('church');
            $table->string('sponsor')->nullable()->after('school_year');
            $table->string('favorite_color')->nullable()->after('sponsor');
            $table->string('favorite_program')->nullable()->after('favorite_color');
            $table->string('favorite_game')->nullable()->after('favorite_program');
            $table->string('favorite_hymn')->nullable()->after('favorite_game');
            $table->string('hobby')->nullable()->after('favorite_hymn');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'church',
                'school_year',
                'sponsor',
                'favorite_color',
                'favorite_program',
                'favorite_game',
                'favorite_hymn',
                'hobby',
            ]);
        });
    }
};
