<?php

namespace App\Jobs;

use App\User;
use App\Jobs\Job;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;



class SendReminderEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;
    
    protected $user;
    
    /**
     * 创建一个新的任务实例
     *
     * @param  User  $user
     * @return void
     */
    public function __construct(User $user)
    {
    	$this->user = $user;
    }
    
    /**
     * 执行任务
     *
     * @param  Mailer  $mailer
     * @return void
     */
    public function handle(Mailer $mailer)
    {
    	$mailer->send('emails.reminder', ['user' => $this->user], function ($m) {
    		//
    	});
    
    	$this->user->reminders()->create();
    }
}

