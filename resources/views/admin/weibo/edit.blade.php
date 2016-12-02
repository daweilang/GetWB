@extends('admin.app')

@section('content')
<div class="container">  
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">编辑 {{ $weibo->wb_title }}</div>
                <div class="panel-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>编辑失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url('admin/weibo/'.$weibo->id) }}" method="POST">
                    	{{ method_field('PATCH') }}
                        {!! csrf_field() !!}
                        <input type="text" name="title" class="form-control" required="required" placeholder="请输入标题" value="{{ $weibo->wb_title }}">
                        <br>
                        <textarea name="body" rows="10" class="form-control" required="required" placeholder="请输入内容" >{{ $weibo->wb_url }}</textarea>
                        <br>
                        <button class="btn btn-lg btn-info">修改</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection
