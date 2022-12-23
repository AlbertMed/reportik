<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Mod_RPT_SACController;

class SyncProvisionesPagos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:SyncProvisionesPagos';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync SyncProvisionesPagos';

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
        $objeto = new Mod_RPT_SACController();
        $myVariable = $objeto->recibir_pagos();
    }
}
