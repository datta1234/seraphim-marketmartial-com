<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use  App\Jobs\ClearSessions;

class ClearUserSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:clear-user-sessions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clears all current non admin user sessions when not within the login window';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ClearSessions::dispatch();
    }
}
