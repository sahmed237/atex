<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('buyer_profiles', 'verification_status')) {
                $table->string('verification_status', 20)->default('submitted')->after('country');
            }
            if (!Schema::hasColumn('buyer_profiles', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('verification_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->dropColumn(['verification_status', 'approved_at']);
        });
    }
};
