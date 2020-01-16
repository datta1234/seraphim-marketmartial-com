<?php

namespace App\Models\UserManagement;

use Illuminate\Database\Eloquent\Model;
use App\Helpers\Misc\ResolveUuid;
use App\Traits\ModelCache;
use App\Traits\OrganisationSlackChat;
use App\Traits\HasDismissibleActivity;
use App\Observers\OrganisationObserver;
use App\Helpers\Broadcast\Channel;
use App\Helpers\Broadcast\Message;
use App\Models\UserManagement\BrokerageFee;


class Organisation extends Model
{
	/**
	 * @property integer $id
	 * @property string $title
	 * @property boolean $verified
     * @property boolean $slack_text_chat 
	 * @property text $description
	 * @property \Carbon\Carbon $created_at
	 * @property \Carbon\Carbon $updated_at
	 */
    
    use ModelCache;//if you going to use this remember to write the observer for the mode
    use OrganisationSlackChat;// used for slack chats between organisation members
    use HasDismissibleActivity; // activity tracked and dismissible
    
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
        'title',
        'verified',
        'description',
        'slack_text_chat'
    ];

    /**
    *   activityKey - identity for cached data
    */
    protected function activityKey() {
        return $this->id;
    }

    public $message;

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

    public function getOneTimeUuidAttribute()
    {
        return ResolveUuid::getOrganisationUuid($this->id);
    }

    public function getSlackChannelAttribute()
    {
        return $this->slackIntegrations()->where("field", '=', 'channel')->first();
    }

    public function notify($key,$message,$status,$timer = 3000)
    { 
        $this->message = new Message($this->id,$key, $message, $status, $timer);
    }

    public function getNotification()
    {
        return Message::getNotification($this->id);
    }

    public function channelName()
    {
        $channel = strtolower($this->title);
        return substr(snake_case(preg_replace("/[^a-z0-9\_\-\s]/", '', $channel)), 0, 21);
    }

    /**
    * Return relation based of _id_foreign index
    * @return \Illuminate\Database\Eloquent\Builder
    */
    public function brokerageFees()
    {
        return $this->hasMany('App\Models\UserManagement\BrokerageFee', 'organisation_id');
    }

    /**
     * Runs through a predefined config to set up default brokerage fees for each structure.
     *  It will only create defaults if there are none set.
     */
    public function setUpDefaultBrokerageFees() 
    {
        $created_brokerage_fees = array();
        if($this->brokerageFees->isEmpty()) {
            $base_key = 'marketmartial.confirmation_settings.';
            foreach (config('marketmartial.confirmation_settings') as $trade_structure => $market_types) {
                foreach ($market_types as $market_type => $legs) {
                    foreach ($legs as $leg => $value) {
                        $brokerage_fee = $this->brokerageFees()->create([
                            'organisation_id'   => $this->id,
                            'key'               => $base_key.$trade_structure.'.'.$market_type.'.'.$leg,
                            'value'             => $value,
                        ]);
                        $created_brokerage_fees[] = $brokerage_fee;
                    } 
                }
            }
        }
        return $created_brokerage_fees;
    }

    public function resolveBrokerageFee($key)
    {
        $brokerage_fee = $this->brokerageFees->firstWhere('key',$key);
        return is_null($brokerage_fee) ? config($key) : $brokerage_fee->value;
    }

    /**
     * Return a simple or query object based on the search term
     *
     * @param string $term
     * @param string $orderBy
     * @param string $order
     * @param string  $filter
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function basicSearch($term = null,$orderBy="title",$order='ASC', $filter = null)
    {
        if($orderBy == null)
        {
            $orderBy = "title";
        }

        if($order == null)
        {
            $order = "ASC";
        }

        $organisationQuery = Organisation::where( function ($q) use ($term)
        {
            $q->where('title', 'like',"%$term%");
        });

        if($filter !== null) {
            switch ($filter) {
                case 'active':
                    $organisationQuery->where('verified', true);
                    break;
                case 'inactive':
                    $organisationQuery->where('verified', false);
                    break;
                case 'chat_full':
                    $organisationQuery->where('slack_text_chat', true);
                    break;
                case 'chat_limited':
                    $organisationQuery->where('slack_text_chat', false);
                    break;
            }
        }

        $organisationQuery->orderBy($orderBy,$order);

        return $organisationQuery;
    }
}
