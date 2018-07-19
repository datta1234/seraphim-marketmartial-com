<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Helpers\Misc\TimeRestrictions;
use Carbon\Carbon;
use  App\Jobs\UuidGen; 

class UuidTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGenerateUsers()
    {
        $lol = new UuidGen;
        dd($lol->generateUsersUuid(),$lol->generateOrganisationUuid());
    }

}
