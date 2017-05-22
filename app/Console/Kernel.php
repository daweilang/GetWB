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
		
    	/**
    	 * 自定义命令，安装系统需要关闭
    	 * 命令详情见command下程序
    	 */
// 		Commands\CompleteWeiboJob::class,
// 		Commands\RetryWBException::class,
// 		Commands\SyncUsersRedis::class,

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
    	//定时任务，设置抓取用户的全部微博
//     	$schedule->command('comwbjob')->everyMinute();
    }
}
