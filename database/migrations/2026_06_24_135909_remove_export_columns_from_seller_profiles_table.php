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
        \Illuminate\Support\Facades\DB::unprepared("
            DROP TRIGGER IF EXISTS trg_seller_profiles_insert;
            DROP TRIGGER IF EXISTS trg_seller_profiles_update;
            DROP TRIGGER IF EXISTS trg_seller_profiles_delete;
        ");

        Schema::table('seller_profiles', function (Blueprint $table) {
            $table->dropColumn(['trade_capacity', 'years_of_experience', 'export_markets']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'trade_capacity')) {
                $table->string('trade_capacity')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'export_markets')) {
                $table->string('export_markets')->nullable();
            }
        });
    }
};
