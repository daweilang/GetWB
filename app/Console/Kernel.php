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
    	Commands\CompleteWeibo::class,
		Commands\CompleteWeiboJob::class,
//     	Commands\SendEmails::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    	/**
    	 * 分两步获得微博数据，首先获得微博完整信息
    	 */
    	$schedule->command('comwbinfo')->everyMinute();
    	$schedule->command('comwbjob')->everyMinute();
//      $schedule->call(function () {
//         	DB::table('failed_jobs')->delete();
//      })->everyMinute();
    }
}
