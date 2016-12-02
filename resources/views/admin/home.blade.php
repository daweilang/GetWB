@extends('admin/app')

@section('content')
<div class="container">  
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
@endsection