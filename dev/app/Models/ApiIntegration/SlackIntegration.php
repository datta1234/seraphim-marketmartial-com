<?php

namespace App\Models\ApiIntegration;

use Illuminate\Database\Eloquent\Model;

class SlackIntegration extends Model
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
    protected $table = 'slack_integrations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'field', 'value',
    ];
}
