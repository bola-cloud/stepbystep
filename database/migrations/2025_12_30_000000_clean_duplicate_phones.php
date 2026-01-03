<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::transaction(function () {
            // Find phone values that appear more than once (and are not null/empty)
            $duplicates = DB::table('users')
                ->select('phone', DB::raw('COUNT(*) as cnt'))
                ->whereNotNull('phone')
                ->where('phone', '<>', '')
                ->groupBy('phone')
                ->having('cnt', '>', 1)
                ->pluck('phone');

            foreach ($duplicates as $phone) {
                // Keep the earliest id record, nullify others
                $ids = DB::table('users')
                    ->where('phone', $phone)
                    ->orderBy('id')
                    ->pluck('id')
                    ->toArray();

                // remove the first id (keep it)
                array_shift($ids);

                if (!empty($ids)) {
                    DB::table('users')
                        ->whereIn('id', $ids)
                        ->update(['phone' => null]);
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse safely — duplicates were removed by nulling.
    }
};
