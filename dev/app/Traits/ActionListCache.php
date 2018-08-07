<?php

namespace App\Traits;
use Illuminate\Support\Facades\Cache;


trait ActionListCache {

	/**
     * Sets the cached action state of an Organisation relating to a User Market Request.
     *
     * @param \App\Models\MarketRequest\UserMarketRequest->id  	$organisation_id
     * @param \App\Models\MarketRequest\UserMarketRequest->id 	$user_market_request_id
     * @param boolean $action_state
     */
    public function setAction($organisation_id, $user_market_request_id, $action_state)
    {
    	// If the Cached variable does not exist it creates a blank array
        $actionList = Cache::remember(config('marketmartial.cached_keys.action-list'), 1440, function () {
            return [];
        });
        // Adds sets the action state
        $actionList[$organisation_id] = [$user_market_request_id => $action_state];
        // Updates the cached variable
        Cache::put(config('marketmartial.cached_keys.action-list'), $actionList, 1440);
    }

    /**
     * Gets the cached action state of an Organisation relating to a User Market Request.
     *
     * @param \App\Models\MarketRequest\UserMarketRequest->id  	$organisation_id
     * @param \App\Models\MarketRequest\UserMarketRequest->id 	$user_market_request_id
     *
     * @return boolean returns the action state
     */
    public function getAction($organisation_id, $user_market_request_id)
    {
    	$action = Cache::get(config('marketmartial.cached_keys.action-list'));
    	if( $action !== null && array_key_exists($organisation_id,$action) ) {
    		return array_key_exists($user_market_request_id,$action[$organisation_id]) ? $action[$organisation_id][$user_market_request_id] : null;
    	}
    	return null;
    }
}