@extends('admin/app')

@section('content')
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="index.html">平台首页</a></li>
				<li><a>数据统计</a></li>
				<li class="active"><strong>微博授权状态</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
	</div>
	
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">填写微博路径</div>
                <div class="panel-body">
                	{{ $html }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
