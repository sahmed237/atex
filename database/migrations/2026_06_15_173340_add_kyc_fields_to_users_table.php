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
            $table->string('bvn', 11)->nullable()->after('passport');
            $table->string('nin', 11)->nullable()->after('bvn');
            $table->string('bank_name')->nullable()->after('nin');
            $table->string('account_number', 20)->nullable()->after('bank_name');
            $table->string('account_name')->nullable()->after('account_number');
            $table->string('kyc_verification_status', 20)->default('pending')->after('account_name');
            $table->timestamp('kyc_submitted_at')->nullable()->after('kyc_verification_status');
            $table->timestamp('kyc_approved_at')->nullable()->after('kyc_submitted_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'bvn',
                'nin',
                'bank_name',
                'account_number',
                'account_name',
                'kyc_verification_status',
                'kyc_submitted_at',
                'kyc_approved_at',
            ]);
        });
    }
};
