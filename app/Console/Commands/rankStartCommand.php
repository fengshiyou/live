<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class rankStartCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rank_start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        //
        $patch = app_path('python')."/zbrank/src";
        exec('/usr/bin/python '.$patch.'/rank_start.py');
    }
}
