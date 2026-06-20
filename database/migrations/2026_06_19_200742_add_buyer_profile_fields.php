<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            if (!Schema::hasColumn('buyer_profiles', 'phone_number')) {
                $table->string('phone_number')->nullable();
            }
            if (!Schema::hasColumn('buyer_profiles', 'shipping_address')) {
                $table->text('shipping_address')->nullable();
            }
            if (!Schema::hasColumn('buyer_profiles', 'billing_address')) {
                $table->text('billing_address')->nullable();
            }
            if (!Schema::hasColumn('buyer_profiles', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('buyer_profiles', 'state')) {
                $table->string('state')->nullable();
            }
            if (!Schema::hasColumn('buyer_profiles', 'zip_code')) {
                $table->string('zip_code')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('buyer_profiles', function (Blueprint $table) {
            $columns = ['phone_number', 'shipping_address', 'billing_address', 'city', 'state', 'zip_code'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('buyer_profiles', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
