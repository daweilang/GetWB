@extends('admin/app')

@section('content')
<div class="container">  
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">评论管理</div>
                <div class="panel-body">
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            {!! implode('<br>', $errors->all()) !!}
                        </div>
                    @endif

                    @foreach ($comments as $comment)
                        <hr>
                        <div class="article">
                            <h4>{{ $comment->nickname }}</h4>
                            <h5>{{ $comment->email }}</h5>
                            <h6><a href="{{ url('/article/'.$comment->article_id) }}">{{ $comment->hasOneArticle->title }}</a></h6>
                            <div class="content">
                                <p>
                                    {{ $comment->content }}
                                </p>
                            </div>
                        </div>
                        <a href="{{ url('admin/comment/'.$comment->id.'/edit') }}" class="btn btn-success">编辑</a>
                        <form action="{{ url('admin/comment/'.$comment->id) }}" method="POST" style="display: inline;">
                            {{ method_field('DELETE') }}
                            {{ csrf_field() }}
                            <button type="submit" class="btn btn-danger">删除</button>
                        </form>
                    @endforeach

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection