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
		
		Commands\CompleteWeiboJob::class,
		Commands\RetryWBException::class,
		Commands\SetUsersRedis::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    	//定时任务，设置抓取用户的全部微博
//     	$schedule->command('comwbjob')->everyMinute();
    }
}
