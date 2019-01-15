<?php

namespace App\Traits;

trait ResolveTradeStructureSlug {
	
    /**
     * get computer readable slug of trade structure. this should be used as the unique identifier
     *
     * @return String
     */
    public function getTradeStructureSlugAttribute() {
        switch($this->trade_structure_id) {
            case 1:
                return 'outright';
            break;
            case 2:
                return 'risky';
            break;
            case 3:
                return 'calendar';
            break;
            case 4:
                return 'fly';
            break;
            case 5:
                return 'option_switch';
            break;
            case 6:
                return 'efp';
            break;
            case 7:
                return 'rolls';
            break;
            case 8:
                return 'efp_switch';
            break;
            case 9:
                return 'var_swap';
            break;
        };
    }
}