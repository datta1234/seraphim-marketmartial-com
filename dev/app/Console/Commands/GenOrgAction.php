<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Models\UserManagement\Organisation;

class GenOrgAction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:actions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set the each organisations actions take cache variable';

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
        Cache::forget(config('marketmartial.cached_keys.action-list'));
    }
}
