@extends('admin/app')

@section('content')

	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin/') }}">平台首页</a></li>
				<li><a>微博数据</a></li>
				<li class="active"><strong>评论数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
	</div>

	<div class="wrapper wrapper-content animated fadeInRight">

		<div class="row"> 
			    <div class="row">
			        <div class="col-md-10 col-md-offset-1">
			            <div class="panel panel-default">
			                <div class="panel-heading">微博评论分析系统</div>
			
			                <div class="panel-body">
			
			                    <a href="{{ url('admin/weibo') }}" class="btn btn-lg btn-success col-xs-12">管理任务</a>
			
			                </div>
			            </div>
			        </div>
			</div> 
		</div>
	</div> 
@endsection