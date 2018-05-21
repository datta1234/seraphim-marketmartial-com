<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
	/**
	 * @property integer $id
	 * @property integer $item_type_id
	 * @property integer $trade_structure_group_id
	 * @property string $title
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'items';
}
