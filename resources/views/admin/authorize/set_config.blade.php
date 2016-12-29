@extends('admin/app')

@section('content')
<meta http-equiv="refresh" content="1;url={{ url('admin/authorize/getPreParam') }}"> 
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"></div>
                <div class="panel-body">
                        <div class="form-group">
                            <label class="col-md control-label">正在获取新浪微博Cookie，页面需要多次跳转，请稍后</label>
                            <br>
                            <label class="col-md control-label">如果系统未能授权，请重试！</label>
                        </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
