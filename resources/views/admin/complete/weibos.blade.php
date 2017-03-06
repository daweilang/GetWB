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
	                <div class="panel-heading"><a href="http://weibo.com/u/{{ $userinfo->uid }}" target='_bank'>{{ $userinfo->username }}</a></div>
	                <div class="panel-body">截止 {{ $userinfo->updated_at }},  显示发布 {{ $userinfo->weibos }} 条微博数据</div>
	                <div class="panel-body">抓取 {{ $count }} 条微博数据</div>
	                @if (count($weibos) > 0)
	                	<div class="panel-body">重新&nbsp;&nbsp;<a href="{{ url('admin/complete/settingWB/'.$userinfo->uid) }}" class="btn btn-danger">设置</a>&nbsp;&nbsp;任务 获取用户微博数据
	                		</div>
	                	<div class="panel-body">
	                		设置&nbsp;&nbsp;<a href="{{ url('admin/complete/setGetAll/'.$userinfo->uid) }}" class="btn btn-danger">全局任务</a>&nbsp;&nbsp;获取用户所有微博的赞、转发、评论数据 &nbsp;&nbsp;
	                	</div>
	             	@else
	             		<div class="panel-body"><a href="{{ url('admin/complete/settingWB/'.$userinfo->uid) }}" class="btn btn-danger">设置</a>&nbsp;&nbsp;任务 获取用户微博数据</div>
	             	@endif
	             </div>
	         </div>
	    </div>
		
		@if (count($weibos) > 0)
		<div class="search-form">
			<div class="col-sm-6">
                            <form action="{{ url('admin/complete/'.$userinfo->uid.'/weibos') }}" method="get">
                                <div class="input-group">
                                    <input placeholder="按mid查找微博" name="mid" class="form-control input-lg" type="text">
                                    <div class="input-group-btn">
                                        <button class="btn btn-lg btn-primary" type="submit">
                                            搜索
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <br>
        	</div>
		<div class="row">
            <div class="col-sm-12">
				
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
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 120px;">mid</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;" >评论</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;" >赞</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;" >转发</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;" >发布时间</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;" >状态</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;" >操作</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($weibos as $weibo)
									<tr class="gradeA odd">
										<td class="center"><a href="http://weibo.com/{{ $userinfo->uid }}/{{ $weibo->code }}" target='_bank'>{{ $weibo->code }}</a></td>
										<td class="center">{{ $weibo->mid }}</td>
										<td class="center">{{ $weibo->comment_total }}</td>
										<td class="center">{{ $weibo->like_total }}</td>
										<td class="center">{{ $weibo->forward_total }}</td>
										<td class="center">{{ $weibo->wb_created }}</td>
										<td class="center">{{ $weibo->status }}</td>
										<td class="center">
											@if ($weibo->status == 0 || $weibo->status == -2)
											<a href="{{ url('admin/complete/setGetAll/'.$userinfo->uid.'/'.$weibo->mid) }}" class="btn btn-danger">获得数据</a>
											@else
											
											@endif
											</td>
									</tr>
									@endforeach
								</tbody>
								<tfoot>
								</tfoot>
							</table>
							<div class="row">
								<div class="col-sm-4"></div>
								<div class="col-sm-8">
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