<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Jobs\MyJob;

use Config;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//         $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
//     	return phpinfo();
        return view('home');
    }
    
    public function job()
    {
    	$queueId = $this->dispatch(new MyJob('key_'.str_random(4), str_random(10)));
    	dd($queueId);
    }
}
