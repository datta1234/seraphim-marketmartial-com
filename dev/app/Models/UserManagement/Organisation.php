<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;

class Organisation extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property boolean $verified
	 * @property text $description
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'organisations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'verified', 'description',
    ];

    /**
     * Return relation based of _id_foreign index
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function slackIntegrations()
    {
        return $this->belongsToMany('App\Models\ApiIntegration\SlackIntegration', 'organisation_slack_intergration', 'organisation_id', 'slack_integration_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function users()
    {
        return $this->hasMany('App\Models\UserManagement\User', 'organisation_id');
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function rebates()
    {
        return $this->hasMany('App\Models\Trade\Rebate', 'organisation_id');
    }
}
