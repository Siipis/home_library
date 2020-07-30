<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Production extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'production';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize the environment for production';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('config:cache');
        $this->call('route:cache');
        $this->call('view:cache');
        $this->call('twig:clean');

        return 0;
    }
}
