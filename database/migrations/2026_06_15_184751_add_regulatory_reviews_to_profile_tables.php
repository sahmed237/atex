<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = ['exporter_profiles', 'buyer_profiles', 'logistics_profiles', 'field_officer_profiles'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->json('regulatory_reviews')->nullable();
            });
        }
    }

    public function down(): void
    {
        $tables = ['exporter_profiles', 'buyer_profiles', 'logistics_profiles', 'field_officer_profiles'];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('regulatory_reviews');
            });
        }
    }
};
