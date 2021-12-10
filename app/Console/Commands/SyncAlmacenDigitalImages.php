<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Http\Controllers\DigitalStorage;

class SyncAlmacenDigitalImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:AlmacenDigitalImages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Almacen Digital Images -- schedule every 5 mins';

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
        $this->comment("Strating to sync Images.. please wait");
        $sync->validateImages();
        $this->comment("Syncronization completed... Thanks for playing");
    }
}
