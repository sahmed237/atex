<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->integer('readiness_score')->default(0)->after('verification_status');
            $table->dateTime('approved_at')->nullable()->after('readiness_score');
        });

        Schema::table('logistics_profiles', function (Blueprint $table) {
            $table->integer('readiness_score')->default(0)->after('verification_status');
            $table->dateTime('approved_at')->nullable()->after('readiness_score');
        });
    }

    public function down(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->dropColumn(['readiness_score', 'approved_at']);
        });

        Schema::table('logistics_profiles', function (Blueprint $table) {
            $table->dropColumn(['readiness_score', 'approved_at']);
        });
    }
};
