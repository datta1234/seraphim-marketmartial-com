<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class TradeStructure extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'trade_structures';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'has_structure_fee',
        'fee_percentage'
    ];

    protected $casts = [
        'is_selectable' => 'boolean',
        'has_structure_fee' => 'boolean'
    ];

    /**
    * Return relation based of market_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function tradeStructureGroups()
    {
        return $this->hasMany('App\Models\StructureItems\TradeStructureGroup', 'trade_structure_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function userMarketRequests()
    {
        return $this->hasMany(
            'App\Models\MarketRequest\UserMarketRequest','trade_structure_id');
    }

    public static function saveFullStructure($structure)
    {
         $groups = $structure['trade_structure_group'];
         unset($structure['trade_structure_group']);
         $tradeStucture = self::create($structure);

         foreach ($groups as $group) 
         {  
              $group['trade_structure_id'] = $tradeStucture->id;//place the id 
              $items = $group['items'];
              unset($group['items']);
              $structureGroup = TradeStructureGroup::create($group);

              foreach ($items as $item) 
              {
                  $item['trade_structure_group_id'] = $structureGroup->id;//place the id 
                  $structureGroup->items[] = Item::create($item);
              }
             $tradeStucture->tradeStructureGroups[] = $structureGroup;

         }
         return $tradeStucture;
    }  

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function marketTypes()
    {
        return $this->belongsToMany('App\Models\StructureItems\MarketType', 'market_types_trade_structures', 'trade_structure_id', 'market_type_id');
    }

}
