@extends('admin/app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">填写用户主页</div>
                <div class="panel-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>新增失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url($path) }}" method="POST">
                        {!! csrf_field() !!}
                        <input type="text" name="wb_url" class="form-control" required="required" placeholder="微博地址">
                        <br>
                        <button class="btn btn-lg btn-info">增加</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection
