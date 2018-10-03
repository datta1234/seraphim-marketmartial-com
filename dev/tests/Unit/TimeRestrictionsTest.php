<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\Misc\TimeRestrictions;
use Carbon\Carbon;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCanLoginRestriction()
    {
		$startTime = Carbon::createFromTimeString(config('marketmartial.window.operation_start_time'));
        $this->assertFalse(TimeRestrictions::canLogin($startTime->subHours(2)));
    }

    public function testCanTrade()
    {
    	$startTime = Carbon::createFromTimeString(config('marketmartial.window.trade_start_time'));
        $this->assertFalse(TimeRestrictions::canLogin($startTime->subHours(2)));
    }
}
