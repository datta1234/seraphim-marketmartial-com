<?php

namespace App\Models\ApiIntegration;

use Illuminate\Database\Eloquent\Model;

class JseIntergration extends Model
{	
	/**
	 * @property integer $id
	 * @property string $type
	 * @property string $field
	 * @property text $value
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'jse_intergrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'field', 'value',
    ];

    /**
    * Return relation based of jse_intergration_id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function markets()
    {
        return $this->belongsToMany('App\Models\StructureItems\Market', 'market_jse_intergration', 'market_id', 'jse_intergration_id');
    }

    /**
     * Return relation based of jse_intergration_id_foreign index
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function stocks()
    {
        return $this->belongsToMany('App\Models\StructureItems\Stock', 'jse_intergration_stock', 'stock_id', 'jse_intergration_id');
    }
}
