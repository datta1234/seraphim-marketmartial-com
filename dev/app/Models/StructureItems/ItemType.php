<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class ItemType extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property string $validation_rule
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'item_types';
}
