<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'seller_tier')) {
                $table->string('seller_tier', 20)->default('local')->after('verification_status');
            }
            if (!Schema::hasColumn('seller_profiles', 'business_description')) {
                $table->text('business_description')->nullable()->after('business_name');
            }
            if (!Schema::hasColumn('seller_profiles', 'business_category')) {
                $table->string('business_category')->nullable()->after('business_type');
            }
            if (!Schema::hasColumn('seller_profiles', 'city')) {
                $table->string('city')->nullable()->after('state');
            }
            if (!Schema::hasColumn('seller_profiles', 'phone')) {
                $table->string('phone')->nullable()->after('city');
            }
            if (!Schema::hasColumn('seller_profiles', 'years_of_experience')) {
                $table->integer('years_of_experience')->nullable()->after('trade_capacity');
            }
            if (!Schema::hasColumn('seller_profiles', 'export_markets')) {
                $table->string('export_markets')->nullable()->after('years_of_experience');
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $columns = ['seller_tier', 'business_description', 'business_category', 'city', 'phone', 'years_of_experience', 'export_markets'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('seller_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
