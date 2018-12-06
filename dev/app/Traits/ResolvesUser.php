<?php

namespace App\Traits;
use App\Helpers\Misc\ResolveUuid;

trait ResolvesUser {
    private $org_context;

    protected function resolveOrganisationId() {
        if($this->org_context) {
            if($this->org_context === "admin") {
                return null;   
            }
            return $this->org_context->id;
        }
        if(\Auth::user() && \Auth::user()->organisation_id) {
            return \Auth::user()->organisation_id;
        }
        return null;
    }

    protected function resolveOrganisation() {
        if($this->org_context) {
            return $this->org_context;
        }
        if(\Auth::user()) { 
            if(\Auth::user()->organisation_id) {
                return \Auth::user()->organisation;
            }
            if($this->org_context == null && \Auth::user()->isAdmin()) {
                $this->org_context = "admin";
                return $this->org_context;
            }
        }
        return null;
    }

    public function setOrgContext($organisation) {
        $this->org_context = $organisation;
        return $this;
    }

    public function isAdminContext() {
        return $this->resolveOrganisation() === "admin";
    }
}
//