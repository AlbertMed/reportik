<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Http\Controllers\DigitalStorage;

class SyncAlmacenDigital extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:AlmacenDigital';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Almacen Digital on demand -- scheduled every 5 mins by default';

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
        $sync = new DigitalStorage;
        $this->comment("Strating to sync Almacen Digital.. please wait");
        $sync->syncDatabases();
        $this->comment("Syncronization completed... Thanks for playing");
    }
}
