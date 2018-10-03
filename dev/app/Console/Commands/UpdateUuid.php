<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UuidGen;

class UpdateUuid extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gen:uuid';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update the uinque id\'s for users and organisations';

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
          UuidGen::dispatch();
    }
}
