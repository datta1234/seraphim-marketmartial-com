<?php

namespace App\Traits;

trait ResolvesUser {
    protected function resolveUserId() {
        if(\Auth::user()) {
            return \Auth::user()->id;
        } else {
            return null;
        }
    }

    protected function resolveOrganisationId() {
        if(\Auth::user()) {
            return \Auth::user()->organisation_id;
        } else {
            return null;
        }
    }    
}