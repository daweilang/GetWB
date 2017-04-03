@extends('admin/app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="{{ url('admin') }}">平台首页</a></li>
				<li><a >数据统计</a></li>
				<li><a href="{{ url('admin/weibo') }}">统计任务</a></li>
				<li class="active"><strong>{{ $typeName }}数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row">
	        <div class="col-md-8 col-md-offset-2">
	            <div class="panel panel-default">
	                <div class="panel-heading">weibo信息</div>
	                <div class="panel-heading"><a href="{{ $weibo->wb_url }}" target='_bank'>{{ $weibo->title }}</a></div>
	                <div class="panel-body">截止 {{ $weibo->updated_at }}，该微博显示有 {{ $weibo->comment_total }} 条评论，{{ $weibo->forward_total }} 条转发，{{ $weibo->like_total }} 条赞</div>
					@if($dataLogs->total())
					<div class="panel-body">完成了 {{ $dataLogs->total()}} 条抓取任务，抓取 {{ $dataCount}} 条 <strong>{{ $typeName }}</strong> 数据 </div>
	                @endif 
	                <div class="panel-body">如需获得数据，请 <a href="{{ url('admin/jobLogs/settingJob/'.$type.'/'.$weibo->mid) }}" class="btn btn-danger">设置</a> 后台队列抓取数据</div>
	                
	             </div>
	         </div>
	    </div>
	
		@if($dataLogs->total())
		<div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>{{ $typeName }}任务列表</h5>
                        <div class="ibox-tools">
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
							<table class="table table-striped table-bordered table-hover  dataTable" id="editable" aria-describedby="editable_info">
								<thead>
									<tr role="row">
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 120px;">页号</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;">{{$typeName}}数</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;">获取状态</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 130px;">设置时间</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 130px;">完成时间</th>
										<!--<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 214px;">操作</th>-->
									</tr>
								</thead>
								<tbody>
									@foreach ($dataLogs as $log)
									<tr class="gradeA odd">
										<td class="sorting_1">{{ $log->j_page }}</td>
										<td class="center ">{{ $log->j_total }}</td>
										<td class="center ">{{ $log->j_status }}</td>
										<td class="center ">{{ $log->created_at }}</td>
										<td class="center ">{{ $log->updated_at }}</td>
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
										{{ $dataLogs->links() }}
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