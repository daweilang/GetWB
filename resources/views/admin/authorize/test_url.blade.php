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
                    <form class="form-horizontal" role="form" method="POST" action="{{ url('/admin/authorize/getTestContent') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('wb_url') ? ' has-error' : '' }}">
                            <label class="col-md-4 control-label">微博页面地址</label>

                            <div class="col-md-6">
                                <input id="wb_url" type="text" class="form-control" name="wb_url" value="">
								@if ($errors->has('wb_url'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('wb_url') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-btn fa-sign-in"></i> 测试
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
