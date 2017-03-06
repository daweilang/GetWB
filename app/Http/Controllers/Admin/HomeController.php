<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class HomeController extends Controller
{
	//设置组名
	public function __construct()
	{
		view()->share('groupName', 'admin');
	}
	
	public function index(Request $request)
	{
		return view('admin/home');
	}
}
