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

	<div class="wrapper wrapper-content  animated fadeInRight blog">

		<div class="row"> 
                <div class="ibox">
                    <div class="ibox-content">
						<h2>模拟登录新浪微博，获得微博cookie</h2>
                        <p>获取新浪微博数据的基础，设置抓取任务前需要登录获得微博授权，cookie失效后需要重新登录。
                        </p>
                        <div class="small m-b-xs">
                            <i class="fa fa-clock-o"></i> 该功能待完善 <br> 
                            <li style="list-style-type:none;"><strong>一、</strong> <span class="text-muted"> 当前只支持使用单用户登录授权，设计使用多用户授权轮询抓取方式，以提高抓取成功率 </span></li>
                            <li style="list-style-type:none;"><strong>二、</strong> <span class="text-muted"> 需要监控机制，应对应登录失效，和其他错误 </span></li>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <a href='/admin/authorize'><button class="btn btn-primary btn-xs" type="button">微博授权</button></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ibox">
                    <div class="ibox-content">
						<h2>基础微博数据获取</h2>
                        <p>根据微博地址，分析微博信息和数据结构，获得微博的全部数据，包括赞、转发和评论等。
                        </p>
                        <div class="small m-b-xs">
                        	<li style="list-style-type:none;"><strong>针对微博用户及其粉丝活跃情况进行分析时，微博的赞、转发和评论是基础的数据。</strong> </li>
                        	<li style="list-style-type:none;">微博基础数据的获得也可能积累大量微博用户信息</li><br>
                            <i class="fa fa-clock-o"></i> 累积数据量巨大，需要重新设计存储架构                     
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-6">
                                <a href='/admin/weibo'><button class="btn btn-primary btn-xs" type="button">统计任务</button></a>
                            </div>
                        </div>
                    </div>
                </div>
		</div>
	</div> 
@endsection