<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateTradeStructureJSON extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mm:genTradeStructures';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Trade Structure JSON for front end fron current state';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // load Trade Structures
        $tradeStructures = \App\Models\StructureItems\TradeStructure::select([
            'title', 
            'id'
        ])->with([ 
            'tradeStructureGroups' => function($query) {
                $query->select([
                    'title', 
                    'id', 
                    'trade_structure_id'
                ]);
            }, 
            'tradeStructureGroups.items' => function($query) {
                $query->select([
                    'title', 
                    'id', 
                    'trade_structure_group_id', 
                    'item_type_id'
                ]);
            }, 
            'tradeStructureGroups.items.itemType' => function($query) {
                $query->select([
                    'title', 
                    'id'
                ]);
            }
        ])->get();

        // mutate TradeStructures
        $tradeStructures = $tradeStructures->map(function($ts) {

            unset($ts->id);
            $ts->key = strtoupper($ts->title);

            // Mutate TradeStructureGroups
            $ts->tradeStructureGroups->map(function($tsg) {

                $tsg->key = strtoupper($tsg->title);
                unset($tsg['id']);
                unset($tsg['trade_structure_id']);

                // Mutate Trade Items
                $tsg->items->map(function($tsgi) {

                    $tsgi->key = strtoupper($tsgi->title);
                    unset($tsgi['id']);
                    unset($tsgi['trade_structure_group_id']);
                    unset($tsgi['item_type_id']);

                    // Overwrite Type relation to field
                    $tsgi->item_type = $tsgi->itemType->title;
                    unset($tsgi->itemType);

                    return $tsgi;
                });
                return $tsg;
            });
            return $ts;
        });

        $destPath = public_path().'/config/';
        $destFile = 'trade_structures.json';

        // dd(public_path('config/trade_structures.json'));
        if (!is_dir($destPath)) {  
            mkdir($destPath,0777,true);  
        }
        \File::put($destPath.$destFile, $tradeStructures->toJSON());
    }
}
