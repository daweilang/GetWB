@extends('admin/app')


@section('style')
<link href="/css/plugins/dataTables/dataTables.bootstrap.css" rel="stylesheet">
@endsection('style')


@section('javascript')
	<script src="/js/plugins/dataTables/jquery.dataTables.js"></script>
	<script src="/js/plugins/dataTables/dataTables.bootstrap.js"></script>
@endsection



@section('content')
	<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="index.html">平台首页</a></li>
				<li><a>微博数据</a></li>
				<li class="active"><strong>评论数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
	</div>

	<div class="wrapper wrapper-content animated fadeInRight">

		<div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>可编辑表格</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                            <a class="dropdown-toggle" data-toggle="dropdown" href="table_data_tables.html#">
                                <i class="fa fa-wrench"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-user">
                                <li><a href="table_data_tables.html#">选项1</a>
                                </li>
                                <li><a href="table_data_tables.html#">选项2</a>
                                </li>
                            </ul>
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="">
                            <a href="{{ url('admin/weibo/create') }}" class="btn btn-primary ">新增</a>
                        </div>
                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid"><div class="row">
<!--                         	<div class="col-sm-6"> -->
<!--                         	<div class="dataTables_length" id="editable_length"><label>每页  -->
<!--                         	<select name="editable_length" aria-controls="editable" class="form-control input-sm"> -->
<!--                         		<option value="10">10</option> -->
<!--                         		<option value="25">25</option> -->
<!--                         		<option value="50">50</option> -->
<!--                         		<option value="100">100</option> -->
<!--                         		</select> 条记录</label> -->
<!--                         		</div> -->
<!--                         	</div> -->
                        	<table class="table table-striped table-bordered table-hover  dataTable" id="editable" aria-describedby="editable_info">
                            <thead>
                                <tr role="row">
                                <th class="sorting_asc" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 333px;" aria-sort="ascending" aria-label="任务名：激活排序列升序">任务名</th>
                                <th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 100px;" aria-label="平台：激活排序列升序">任务状态</th>
                                <th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 133px;" aria-label="引擎版本：激活排序列升序">评论数</th>
                                <th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 234px;" aria-label="CSS等级：激活排序列升序">添加时间</th>
                                <th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 214px;" >操作</th></tr>
                            </thead>
                            <tbody>
                            
                   			@foreach ($weibos as $weibo)
                            <tr class="gradeA odd">
                             		
                                    <td class="sorting_1"><a href="{{ $weibo->wb_url }}" target="_blank">{{ $weibo->wb_title }}</a></td>
                                    <td class=" ">{{ $weibo->wb_status }}</td>
                                    <td class="center ">{{ $weibo->wb_comment_total }}</td>
                                    <td class="center ">{{ $weibo->updated_at }}</td>
                                    <td class="center ">
                                    	<a href="{{ url('admin/weibo/'.$weibo->id.'/edit') }}" class="btn btn-success">编辑</a>
                        				<a href="{{ url('admin/weibo/getwbcoo/'.$weibo->id) }}" class="btn btn-danger">队列任务</a>
                        			</td>                     			
                                </tr>
                            @endforeach
                             
                            <tr class="gradeA even">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Firefox 1.5</td>
                                    <td class=" ">Win 98+ / OSX.2+</td>
                                    <td class="center ">1.8</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA odd">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Firefox 2.0</td>
                                    <td class=" ">Win 98+ / OSX.2+</td>
                                    <td class="center ">1.8</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA even">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Firefox 3.0</td>
                                    <td class=" ">Win 2k+ / OSX.3+</td>
                                    <td class="center ">1.9</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA odd">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Camino 1.0</td>
                                    <td class=" ">OSX.2+</td>
                                    <td class="center ">1.8</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA even">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Camino 1.5</td>
                                    <td class=" ">OSX.3+</td>
                                    <td class="center ">1.8</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA odd">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Netscape 7.2</td>
                                    <td class=" ">Win 95+ / Mac OS 8.6-9.2</td>
                                    <td class="center ">1.7</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA even">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Netscape Browser 8</td>
                                    <td class=" ">Win 98SE+</td>
                                    <td class="center ">1.7</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA odd">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Netscape Navigator 9</td>
                                    <td class=" ">Win 98+ / OSX.2+</td>
                                    <td class="center ">1.8</td>
                                    <td class="center ">A</td>
                                </tr><tr class="gradeA even">
                                    <td class="sorting_1">Gecko</td>
                                    <td class=" ">Mozilla 1.0</td>
                                    <td class=" ">Win 95+ / OSX.1+</td>
                                    <td class="center ">1</td>
                                    <td class="center ">A</td>
                                </tr></tbody>
                            <tfoot>
                                <tr><th rowspan="1" colspan="1">渲染引擎</th><th rowspan="1" colspan="1">浏览器</th><th rowspan="1" colspan="1">平台</th><th rowspan="1" colspan="1">引擎版本</th><th rowspan="1" colspan="1">CSS等级</th></tr>
                            </tfoot>
                        </table><div class="row"><div class="col-sm-6"><div class="dataTables_info" id="editable_info" role="alert" aria-live="polite" aria-relevant="all">显示 1 到 10 项，共 57 项</div></div><div class="col-sm-6"><div class="dataTables_paginate paging_simple_numbers" id="editable_paginate"><ul class="pagination"><li class="paginate_button previous disabled" aria-controls="editable" tabindex="0" id="editable_previous"><a href="#">上一页</a></li><li class="paginate_button active" aria-controls="editable" tabindex="0"><a href="#">1</a></li><li class="paginate_button " aria-controls="editable" tabindex="0"><a href="#">2</a></li><li class="paginate_button " aria-controls="editable" tabindex="0"><a href="#">3</a></li><li class="paginate_button " aria-controls="editable" tabindex="0"><a href="#">4</a></li><li class="paginate_button " aria-controls="editable" tabindex="0"><a href="#">5</a></li><li class="paginate_button " aria-controls="editable" tabindex="0"><a href="#">6</a></li><li class="paginate_button next" aria-controls="editable" tabindex="0" id="editable_next"><a href="#">下一页</a></li></ul></div></div></div></div>

                    </div>
                </div>
            </div>
        </div>
	</div>
</div>
@endsection

