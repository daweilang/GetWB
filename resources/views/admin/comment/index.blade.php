@extends('admin/app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="index.html">平台首页</a></li>
				<li><a>数据统计</a></li>
				<li class="active"><strong>评论数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
		
	<div class="container">  
	    <div class="row">
	        <div class="col-md-10 col-md-offset-1">
	            <div class="panel panel-default">
	                <div class="panel-heading">评论管理</div>
	                <div class="panel-body">
	                    @if (count($errors) > 0)
	                        <div class="alert alert-danger">
	                            {!! implode('<br>', $errors->all()) !!}
	                        </div>
	                    @endif
						@if (count($wb_comment_jobs) > 0)
		                    @foreach ($wb_comment_jobs as $comment)
		                        <hr>
		                        <a href="{{ url('admin/comment/'.$comment->id.'/edit') }}" class="btn btn-success">编辑</a>
		                        <form action="{{ url('admin/comment/'.$comment->id) }}" method="POST" style="display: inline;">
		                            {{ method_field('DELETE') }}
		                            {{ csrf_field() }}
		                            <button type="submit" class="btn btn-danger">删除</button>
		                        </form>
		                    @endforeach
	                    @else if
	
	                    
	                    @endif
	
	                </div>
	            </div>
	        </div>
	    </div>
	</div> 
</div>
@endsection