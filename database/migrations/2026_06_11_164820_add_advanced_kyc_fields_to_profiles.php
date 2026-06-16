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
        Schema::table('exporter_profiles', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('trade_capacity')->nullable();
        });

        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('trade_capacity')->nullable();
        });

        Schema::table('logistics_profiles', function (Blueprint $table) {
            $table->string('bank_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_name')->nullable();
            $table->string('fleet_size')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exporter_profiles', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_number', 'account_name', 'trade_capacity']);
        });

        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_number', 'account_name', 'trade_capacity']);
        });

        Schema::table('logistics_profiles', function (Blueprint $table) {
            $table->dropColumn(['bank_name', 'account_number', 'account_name', 'fleet_size']);
        });
    }
};
