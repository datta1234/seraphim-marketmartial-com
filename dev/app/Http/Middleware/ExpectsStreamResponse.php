<?php

namespace App\Http\Middleware;

use Closure;
use App\Helpers\Misc\ResolveUuid;

class ExpectsStreamResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // only if user present and valid proceed with header addition
        $user = $request->user();
        if($user) {
            $uuid = null;
            if($user->organisation_id != null) {
                $uuid = ResolveUuid::getOrganisationUuid($user->organisation_id);
                // prefix for private channels to organisations
                $uuid = "private-organisation.".$uuid;
            } elseif($user->isAdmin()) {
                $uuid = "private-organisation.admin";
            }

            if($uuid != null) {
                $activeStreams = \Cache::get('activeStreamData', []);
                // are there any records for this organisation
                if(isset($activeStreams[$uuid]) && !empty($activeStreams[$uuid])) {
                    $activeStreams[$uuid] = array_filter($activeStreams[$uuid], function($item) {
                        return now()->lt($item); // filter to only valid time, now < expiry
                    });
                    $response->header('pending-streams', implode(',', array_keys($activeStreams[$uuid])));
                    unset($activeStreams[$uuid]);
                    \Cache::forever('activeStreamData', $activeStreams);
                }
            }

        }
        return $response;
    }
}
