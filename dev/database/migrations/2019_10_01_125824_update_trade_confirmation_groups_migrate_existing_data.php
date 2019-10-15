<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTradeConfirmationGroupsMigrateExistingData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add TradeConfirmationGroupTypes to migrate data 
        $groups = config('trade_confirmation_groups');

        $tradeConfirmationGroupTypes = [];
        foreach ($groups as $group) 
        {
            $tradeConfirmationGroupTypes[] = factory(
                \App\Models\TradeConfirmations\TradeConfirmationGroupType::class,1
            )
            ->create(['title' => $group['title']]);
        }

        // Update Options Group
        \App\Models\TradeConfirmations\TradeConfirmationGroup::where('is_options',1)
        ->update(["trade_confirmation_group_type_id" => $tradeConfirmationGroupTypes[0][0]->id]);

        // Update Futures Group
        \App\Models\TradeConfirmations\TradeConfirmationGroup::where('is_options',0)
        ->update(["trade_confirmation_group_type_id" => $tradeConfirmationGroupTypes[1][0]->id]);

        // Check for Fees and change to type 3
        \App\Models\TradeConfirmations\TradeConfirmationGroup::whereHas('tradeConfirmationItems',function($q) {
            $q->where('title',"Fee Total");
        })
        ->update(["trade_confirmation_group_type_id" => $tradeConfirmationGroupTypes[2][0]->id]);

        Schema::table('trade_confirmation_groups', function (Blueprint $table){
            $table->dropColumn(['is_options']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('trade_confirmation_groups', function (Blueprint $table){
            $table->boolean('is_options')->default(0);
        });

        $tradeConfirmationGroupTypes = \App\Models\TradeConfirmations\TradeConfirmationGroupType::all();

        // Update Options Group
        \App\Models\TradeConfirmations\TradeConfirmationGroup::where(
            'trade_confirmation_group_type_id',
            $tradeConfirmationGroupTypes[0]->id
        )
        ->update(["is_options" => 1]);

        // Update Futures Group 
        \App\Models\TradeConfirmations\TradeConfirmationGroup::where(
            'trade_confirmation_group_type_id',
            $tradeConfirmationGroupTypes[1]->id
        )
        ->update(["is_options" => 0]);

        // Remove Trade Confirmation group type
        DB::table('trade_confirmation_groups')->update(array('trade_confirmation_group_type_id' => NULL));   

        // Remove seeded Trade Confirmation Group Type data
        DB::statement("SET foreign_key_checks=0");
        \App\Models\TradeConfirmations\TradeConfirmationGroupType::truncate();
        DB::statement("SET foreign_key_checks=1");
    }
}
