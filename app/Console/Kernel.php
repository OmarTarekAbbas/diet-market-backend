<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $rewardPointsNotifications = $this->settingsRepository->getSetting('reward', 'rewardPointsNotifications') ?? 30;
        $schedule->command('notify:30Seconds')->cron('*/30 * * * * *');
        $schedule->command('notify:updateStatusDeliveryMen');
        $schedule->command('notify:sendEmailForCoupon')->cron('0 0 */' . $rewardPointsNotifications . ' * *');
        $schedule->command('notify:sendEmailForRewardCustomer')->cron('0 0 */' . $rewardPointsNotifications . ' * *');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
