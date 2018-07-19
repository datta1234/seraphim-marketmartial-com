<?php

namespace App\Traits;
use App\Helpers\Misc\ResolveUuid;

trait ResolvesUser {
    protected function resolveUserId() {
        if(\Auth::user()) {
            return \Auth::user()->id;
        } else {
            return null;
        }
    }

    protected function resolveOrganisationId() {
        if(\Auth::user() && \Auth::user()->organisation_id) {
            return \Auth::user()->organisation_id;
        } else {
            return null;
        }
    }    
}