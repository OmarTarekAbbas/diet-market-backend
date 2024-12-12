<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateStatusDeliveryMen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:updateStatusDeliveryMen';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The request was assigned to the representative and passed more than 30 seconds without a second, or the request was rejected';

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
        app('App\Modules\DeliveryMen\Controllers\Site\UpdateAccountController')->cronJobsForUpdateDelivery();
    }
}
