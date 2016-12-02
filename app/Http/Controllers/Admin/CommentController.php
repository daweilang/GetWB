<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Models\Comment;

class CommentController extends Controller
{
	
    //设置组名
    public function __construct()
    {
    	view()->share('groupName', 'weibo');
    }
	
	
	//
	public function index()
	{
// 		DB::connection()->enableQueryLog();
// 		var_dump(DB::getQueryLog());
		return view('admin/comment/index')->withComments(Comment::with('hasOneArticle')->get());
	}
	
	
	public function edit($id)
	{
// 		return view('admin/comment/edit')->withComment(Comment::find($id));
		return view('admin/comment/edit')->withComment(Comment::with('hasOneArticle')->find($id));
	}
	
	public function update(Request $request, $id)
	{
		$this->validate($request, [
				'content' => 'required',
		]);
		$comment = Comment::find($id);
		$comment->content = $request->get('content');
	
		if($comment->save()){
			return redirect('admin/comment');
		}
		else{
			return redirect()->back()->withInput()->withErrors('更新失败');
		}
	}
	
	public function destroy($id)
	{
		Comment::find($id)->delete();
		return redirect()->back()->withInput()->withErrors('删除成功！');
	}
	
}
