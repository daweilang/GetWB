@extends('admin/app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="index.html">平台首页</a></li>
				<li><a>数据统计</a></li>
				<li class="active"><strong>评论数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row">
	        <div class="col-md-8 col-md-offset-2">
	            <div class="panel panel-default">
	                <div class="panel-heading">weibo信息</div>
	                <div class="panel-heading"><a href="{{ $weibo->wb_url }}" target='_bank'>{{ $weibo->wb_title }}</a></div>
	                <div class="panel-body">截止 {{ $weibo->updated_at }}, 共{{ $weibo->wb_comment_page }}页, {{ $weibo->wb_comment_total }}条评论</div>
					@if(count($comments)>0)
					<div class="panel-body">设置 ($count($comments)) 条抓取任务，已执行，未完成！</div>
	                @else
	                <div class="panel-body">尚未设置获取评论页任务 <a href="{{ url('admin/commentJob/setting/'.$weibo->mid) }}" class="btn btn-danger">设置</a></div>
	                <div class="panel-body">评论较多时请设置队列获取任务 <a href="{{ url('admin/commentJob/settingJob/'.$weibo->mid) }}" class="btn btn-danger">设置</a></div>
	                @endif 
	                
	             </div>
	         </div>
	    </div>
	
		@if (count($comments) > 0)
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
                    
                    @if (count($errors) > 0)
                    <div class="ibox-title">
                        <h5>可编辑表格</h5>
                        <div class="ibox-tools">
	                        <div class="alert alert-danger">
	                            {!! implode('<br>', $errors->all()) !!}
	                        </div>
                        </div>
                    </div>           
	                @endif

				<div class="ibox-content">
					<div id="editable_wrapper" class="dataTables_wrapper form-inline"
						role="grid">
						<div class="row">
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
							<table
								class="table table-striped table-bordered table-hover  dataTable"
								id="editable" aria-describedby="editable_info">
								<thead>
									<tr role="row">
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 120px;">评论页号</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 100px;"
											aria-label="评论数：激活排序列升序">评论数</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 100px;"
											aria-label="评论数：激活排序列升序">获取状态</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 160px;"
											aria-label="更新时间：激活排序列升序">获取时间</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 214px;">操作</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($comments as $comment)
									<tr class="gradeA odd">

										<td class="sorting_1">{{ $comment->j_comment_page }}</td>
										<td class="center ">{{ $comment->j_comment_total }}</td>
										<td class="center ">{{ $comment->j_status }}</td>
										<td class="center ">{{ $comment->updated_at }}</td>
										<td class="center "><a
											href="{{ url('admin/weibo/'.$comment->id.'/edit') }}"
											class="btn btn-success">重新获取</a></td>
									</tr>
									@endforeach
								</tbody>
								<tfoot>
								</tfoot>
							</table>
							<div class="row">
								<div class="col-sm-5"></div>
								<div class="col-sm-7">
									<div class="dataTables_paginate paging_simple_numbers" id="editable_paginate">
										{{ $comments->links() }}
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
                </div>
            </div>
        </div>
	</div>
		@else
		@endif
</div>
@endsection