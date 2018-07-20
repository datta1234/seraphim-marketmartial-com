<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Misc\ResolveUuid;
use App\Traits\ModelCache;
use App\Observers\OrganisationObserver;
use App\Helpers\Broadcast\Channel;


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
    use ModelCache;//if you going to use this remember to write the observer for the mode

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
        return $this->belongsToMany('App\Models\ApiIntegration\SlackIntegration', 'organisation_slack_intergration', 'slack_integration_id', 'organisation_id');
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

    public static function verifiedCache()
    {
        return Channel::verifiedOrganisationsCached();
    }

    /**
    * Return array of collections
    * @return array
    */
    public static function getUuids()
    {
        return ResolveUuid::getOrganisationsUuid();
    }

    public function getUuidAttribute()
    {
        return ResolveUuid::getOrganisationUuid($this->id);
    }

   
}
