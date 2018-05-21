<?php

namespace App\Models\StructureItems;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
	/**
	 * @property integer $id
	 * @property string $name
	 * @property string $code
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'stocks';
}
