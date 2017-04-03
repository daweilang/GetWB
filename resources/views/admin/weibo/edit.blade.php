@extends('admin.app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="panel panel-default">
                <div class="panel-heading">编辑 {{ $weibo->wb_name }}</div>
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
                        <div class="col-sm-9"  style="margin-bottom: 15px;">
                        <input type="text" name="wb_name" class="form-control" required="required" placeholder="请输入标题" value="{{ $weibo->wb_name }}">
                        <br>
                        <input type="text" name="wb_url" class="form-control" required="required" placeholder="请输入微博地址" value="{{ $weibo->wb_url }}">
                        </div>
                        <br>
                        <div class="col-sm-9"  style="margin-bottom: 20px;">
                        	<strong>提交微博地址后，平台会设置抓取微博信息的队列任务，之后设置抓取赞、评论和转发全部数据的任务</strong>
                        	<p class="form-control-static">
                        		如果微博数据量较大，全部任务执行时间较长，可以选择设置抓取范围，赞、评论和转发的任务管理可以重新设置抓取范围
                        	</p>
                        </div>
                        <br>
                        <div class="col-sm-9" style="margin-bottom: 15px;">
                                    <label class="checkbox-inline">
                                        <input name='wb_scope[]' value="like" id="inlineCheckbox1" type="checkbox" checked="checked">赞</label>
                                    <label class="checkbox-inline">
                                        <input name='wb_scope[]' value="comment" id="inlineCheckbox2" type="checkbox" checked="checked">评论</label>
                                    <label class="checkbox-inline">
                                        <input name='wb_scope[]' value="forward" id="inlineCheckbox3" type="checkbox" checked="checked">转发</label>
                        </div>
	                    <div class="col-sm-9"  style="margin-bottom: 15px;">
	                        <button class="btn btn-lg btn-info">修改</button>
	                    </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>  
@endsection
