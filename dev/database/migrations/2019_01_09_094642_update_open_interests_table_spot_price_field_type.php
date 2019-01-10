<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOpenInterestsTableSpotPriceFieldType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('open_interests', function (Blueprint $table) {
            $table->string('spot_price')->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('open_interests', function (Blueprint $table) {
            /*// Add new spot price collumn
            DB::select('ALTER TABLE `open_interests` 
                ADD COLUMN `new_spot_price` DOUBLE(11,2)'
            );
            // Copy data from old to new - casting does not work on text
            DB::select('UPDATE `open_interests` 
                SET `new_spot_price`=CAST(`spot_price` as DECIMAL(11,2))'
            );
            // Rename old spot price collumn
            DB::select('ALTER TABLE `open_interests`
                CHANGE `spot_price` `old_spot_price` VARCHAR(255) NOT NULL'
            );
            // Rename new spot price collumn
            DB::select('ALTER TABLE `open_interests`
                CHANGE `new_spot_price` `spot_price` DOUBLE(11,2) NOT NULL'
            );
            // Drop old spot price collumn
            DB::select('ALTER TABLE `open_interests` 
                DROP COLUMN `old_spot_price`'
            );*/

            // Truncate table data - if data cannot be truncated this needs to be changed
            DB::select('TRUNCATE `open_interests`');
            // Rename old spot price collumn
            DB::select('ALTER TABLE `open_interests`
                CHANGE `spot_price` `spot_price` DOUBLE(11,2) NOT NULL'
            );
        });
    }
}
