@extends('admin.app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">编辑 {{ $user->wb_name }}</div>
                <div class="panel-body">

                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <strong>编辑失败</strong> 输入不符合要求<br><br>
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    <form action="{{ url($path.'/'.$user->uid) }}" method="POST">
                    	{{ method_field('PATCH') }}
                        {!! csrf_field() !!}
                        <input type="text" name="user_url" class="form-control" value='{{ $user->face }}' disabled="disabled" >
                        <br>
                        <button class="btn btn-lg btn-info">重新分析</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection
