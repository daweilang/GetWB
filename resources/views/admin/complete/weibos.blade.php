@extends('admin/app')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
		<div class="col-lg-10">
			<h2>Data Tables</h2>
			<ol class="breadcrumb">
				<li><a href="index.html">平台首页</a></li>
				<li><a>综合分析</a></li>
				<li class="active"><strong>用户微博</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row">
	        <div class="col-md-8 col-md-offset-2">
	            <div class="panel panel-default">
	                <div class="panel-heading">用户信息</div>
	                <div class="panel-heading"><a href="http://weibo.cn/{{ $userinfo->uid }}" target='_bank'>{{ $userinfo->username }}</a></div>
	                <div class="panel-body">截止 {{ $userinfo->updated_at }},  共 {{ $userinfo->weibos }} 条微博数据</div>
	                <div class="panel-body">抓取 {{ $count }} 条数据</div>
	                <div class="panel-body">设置任务 获取weibo信息 &nbsp;&nbsp;<a href="{{ url('admin/complete/settingWB/'.$userinfo->uid) }}" class="btn btn-danger">设置</a></div>
	             </div>
	         </div>
	    </div>
	
		@if (count($weibos) > 0)
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
							<table
								class="table table-striped table-bordered table-hover  dataTable"
								id="editable" aria-describedby="editable_info">
								<thead>
									<tr role="row">
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 120px;">微博地址</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;" >评论</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;" >赞</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;" >转发</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;" >发布时间</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;" >状态</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($weibos as $weibo)
									<tr class="gradeA odd">
										<td class="center"><a href="http://weibo.com/{{ $userinfo->uid }}/{{ $weibo->code }}" target='_bank'>{{ $weibo->code }}</a></td>
										<td class="center">{{ $weibo->comment_total }}</td>
										<td class="center">{{ $weibo->like_total }}</td>
										<td class="center">{{ $weibo->repost_total }}</td>
										<td class="center">{{ $weibo->wb_created }}</td>
										<td class="center">{{ $weibo->status }}</td>
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
										{{ $weibos->links() }}
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