<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\UserManagement\Session;
use App\Helpers\Misc\TimeRestrictions;
use Carbon\Carbon;

class ClearSessions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $can_login = TimeRestrictions::canLogin(Carbon::now());
        if(!$can_login) {
            Session::whereHas('user', function($q) {
                $q->where('role_id','!=',1);
            })->delete();
        }
    }
}
