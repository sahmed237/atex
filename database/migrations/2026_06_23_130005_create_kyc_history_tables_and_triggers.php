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
        $sellerProfileCols = ["id","user_id","business_name","business_description","registration_number","tax_number","bvn","nin","business_type","business_category","lga","address","seller_program_status","seller_brand_name","fulfillment_model","verification_status","seller_tier","readiness_score","approved_at","created_at","updated_at","bank_name","account_number","account_name","trade_capacity","years_of_experience","export_markets","rejection_reason","regulatory_reviews","country","state","city","phone"];
        $sellerProfileKycCols = ["id","seller_profile_id","full_name","date_of_birth","nationality","residential_address","id_type","id_number","id_front_path","id_back_path","selfie_path","proof_of_address_path","cac_certificate_path","created_at","updated_at"];
        $itemReviewsCols = ["id","owner_type","owner_id","item_key","status","comment","reviewer_id","reviewed_at","created_at","updated_at"];

        // 1. Create Tables
        if (DB::getDriverName() === 'sqlite') {
            $createCols = fn($cols) => implode(', ', array_map(fn($c) => "`$c` TEXT", $cols));
            DB::unprepared("
                CREATE TABLE IF NOT EXISTS seller_profile_hist (hist_id INTEGER PRIMARY KEY AUTOINCREMENT, operation_type TEXT, changed_at DATETIME DEFAULT CURRENT_TIMESTAMP, " . $createCols($sellerProfileCols) . ");
                CREATE TABLE IF NOT EXISTS seller_profile_kyc_hist (hist_id INTEGER PRIMARY KEY AUTOINCREMENT, operation_type TEXT, changed_at DATETIME DEFAULT CURRENT_TIMESTAMP, " . $createCols($sellerProfileKycCols) . ");
                CREATE TABLE IF NOT EXISTS seller_profile_kyc_item_reviews_hist (hist_id INTEGER PRIMARY KEY AUTOINCREMENT, operation_type TEXT, changed_at DATETIME DEFAULT CURRENT_TIMESTAMP, " . $createCols($itemReviewsCols) . ");
            ");
        } else {
            DB::unprepared("
                CREATE TABLE seller_profile_hist AS SELECT * FROM seller_profiles WHERE 1=0;
                ALTER TABLE seller_profile_hist ADD COLUMN hist_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST, ADD COLUMN operation_type VARCHAR(20) AFTER hist_id, ADD COLUMN changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER operation_type;

                CREATE TABLE seller_profile_kyc_hist AS SELECT * FROM seller_profile_kycs WHERE 1=0;
                ALTER TABLE seller_profile_kyc_hist ADD COLUMN hist_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST, ADD COLUMN operation_type VARCHAR(20) AFTER hist_id, ADD COLUMN changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER operation_type;

                CREATE TABLE seller_profile_kyc_item_reviews_hist AS SELECT * FROM seller_profile_kyc_item_reviews WHERE 1=0;
                ALTER TABLE seller_profile_kyc_item_reviews_hist ADD COLUMN hist_id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY FIRST, ADD COLUMN operation_type VARCHAR(20) AFTER hist_id, ADD COLUMN changed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER operation_type;
            ");
        }

        // 2. Create Triggers
        $this->createTriggers('seller_profiles', 'seller_profile_hist', $sellerProfileCols);
        $this->createTriggers('seller_profile_kycs', 'seller_profile_kyc_hist', $sellerProfileKycCols);
        $this->createTriggers('seller_profile_kyc_item_reviews', 'seller_profile_kyc_item_reviews_hist', $itemReviewsCols);
    }

    private function createTriggers($table, $histTable, $cols) {
        $colsList = implode(', ', array_map(fn($c) => "`$c`", $cols));
        $newVals = implode(', ', array_map(fn($c) => "NEW.`$c`", $cols));
        $oldVals = implode(', ', array_map(fn($c) => "OLD.`$c`", $cols));

        DB::unprepared("
            CREATE TRIGGER trg_{$table}_insert AFTER INSERT ON {$table} FOR EACH ROW
            BEGIN
                INSERT INTO {$histTable} (operation_type, {$colsList}) VALUES ('INSERT', {$newVals});
            END;

            CREATE TRIGGER trg_{$table}_update AFTER UPDATE ON {$table} FOR EACH ROW
            BEGIN
                INSERT INTO {$histTable} (operation_type, {$colsList}) VALUES ('UPDATE', {$oldVals});
            END;

            CREATE TRIGGER trg_{$table}_delete AFTER DELETE ON {$table} FOR EACH ROW
            BEGIN
                INSERT INTO {$histTable} (operation_type, {$colsList}) VALUES ('DELETE', {$oldVals});
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("
            DROP TRIGGER IF EXISTS trg_seller_profiles_insert;
            DROP TRIGGER IF EXISTS trg_seller_profiles_update;
            DROP TRIGGER IF EXISTS trg_seller_profiles_delete;

            DROP TRIGGER IF EXISTS trg_seller_profile_kycs_insert;
            DROP TRIGGER IF EXISTS trg_seller_profile_kycs_update;
            DROP TRIGGER IF EXISTS trg_seller_profile_kycs_delete;

            DROP TRIGGER IF EXISTS trg_seller_profile_kyc_item_reviews_insert;
            DROP TRIGGER IF EXISTS trg_seller_profile_kyc_item_reviews_update;
            DROP TRIGGER IF EXISTS trg_seller_profile_kyc_item_reviews_delete;

            DROP TABLE IF EXISTS seller_profile_hist;
            DROP TABLE IF EXISTS seller_profile_kyc_hist;
            DROP TABLE IF EXISTS seller_profile_kyc_item_reviews_hist;
        ");
    }
};
