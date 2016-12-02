@extends('admin/app')

@section('content')
<div class="container">  
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">新增一条微博</div>
                <div class="panel-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>新增失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url('admin/weibo') }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="text" name="wb_title" class="form-control" required="required" placeholder="微博简介">
                        <br>
                        <input type="text" name="wb_url" class="form-control" required="required" placeholder="微博地址">
                        <br>
                        <button class="btn btn-lg btn-info">新增weibo</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection
