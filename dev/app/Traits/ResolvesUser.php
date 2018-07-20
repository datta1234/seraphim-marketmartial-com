<?php

namespace App\Traits;
use App\Helpers\Misc\ResolveUuid;

trait ResolvesUser {
    private $org_context;

    protected function resolveOrganisationId() {
        if($this->org_context) {
            return $this->org_context->id;
        }
        if(\Auth::user() && \Auth::user()->organisation_id) {
            return \Auth::user()->organisation_id;
        } else {
            return null;
        }
    }

    public function setOrgContext($organisation) {
        $this->org_context = $organisation;
        return $this;
    }
}
//