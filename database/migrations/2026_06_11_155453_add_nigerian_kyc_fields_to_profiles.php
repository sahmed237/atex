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
            $table->string('bvn', 11)->nullable()->after('tax_number');
            $table->string('nin', 11)->nullable()->after('bvn');
        });

        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->string('registration_number')->nullable()->after('company_name');
            $table->string('tax_number')->nullable()->after('registration_number');
            $table->string('bvn', 11)->nullable()->after('tax_number');
            $table->string('nin', 11)->nullable()->after('bvn');
            $table->text('address')->nullable()->after('country');
        });

        Schema::table('logistics_profiles', function (Blueprint $table) {
            $table->string('registration_number')->nullable()->after('company_name');
            $table->string('tax_number')->nullable()->after('registration_number');
            $table->string('bvn', 11)->nullable()->after('tax_number');
            $table->string('nin', 11)->nullable()->after('bvn');
            $table->text('address')->nullable()->after('fleet_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exporter_profiles', function (Blueprint $table) {
            $table->dropColumn(['bvn', 'nin']);
        });

        Schema::table('buyer_profiles', function (Blueprint $table) {
            $table->dropColumn(['registration_number', 'tax_number', 'bvn', 'nin', 'address']);
        });

        Schema::table('logistics_profiles', function (Blueprint $table) {
            $table->dropColumn(['registration_number', 'tax_number', 'bvn', 'nin', 'address']);
        });
    }
};
