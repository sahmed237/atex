<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('seller_profiles', 'country')) {
                $table->string('country')->nullable();
            }
            if (!Schema::hasColumn('seller_profiles', 'state')) {
                $table->string('state')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('seller_profiles', function (Blueprint $table) {
            $columns = ['country', 'state'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('seller_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
