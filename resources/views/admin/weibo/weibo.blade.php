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
				<li><a href="{{ url('admin') }}">平台首页</a></li>
				<li><a>数据统计</a></li>
				<li class="active"><strong>微博数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
	</div>

	<div class="wrapper wrapper-content animated fadeInRight">
		<div class="search-form">
			<div class="col-sm-6">
                            <form action="{{ url('admin/weibo') }}" method="get">
                                <div class="input-group">
                                    <input placeholder="按mid查找微博" name="mid" class="form-control input-lg" type="text" value='{{ $mid }}'>
                                    <div class="input-group-btn">
                                        <button class="btn btn-lg btn-primary" type="submit">
                                            搜索
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <br>
        	</div>
        </div>
		<div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-content">
                        <div class="">
                            <a href="{{ url('admin/weibo/create') }}" class="btn btn-primary ">新增</a>
                            <a href="{{ url('admin/authorize/test') }}" class="btn btn-primary ">测试微博授权</a>
                        </div>
                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
						<div class="row">
							<table class="table table-striped table-bordered table-hover  dataTable" id="editable" aria-describedby="editable_info">
								<thead>
									<tr role="row">
										<th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" style="width: 140px;">任务名</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 110px;">mid</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 130px;">微博题目</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;">评论</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;">转发</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;">赞</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 130px;">更新时间</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;">操作</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($weibos as $weibo)
									<tr class="gradeA odd">

										<td class="sorting_1">{{ $weibo->wb_name }}</a></td>
										<td class="center">{{ $weibo->mid }}</td>
										<td class="center"><a href="{{ $weibo->wb_url }}" target="_blank">{{ $weibo->title }}</a></td>
										<td class="center">
											@if($weibo->mid) 
												<a href="{{ url('admin/jobLogs/comment/'.$weibo->mid) }}">{{ $weibo->comment_total }}</a>
											@else
											{{ $weibo->comment_total }}
											@endif</td>
										<td class="center">
											@if($weibo->mid) 
												<a href="{{ url('admin/jobLogs/forward/'.$weibo->mid) }}">{{ $weibo->forward_total }}</a>
											@else
											{{ $weibo->forward_total }}
											@endif</td>
										<td class="center">
											@if($weibo->mid) 
												<a href="{{ url('admin/jobLogs/like/'.$weibo->mid) }}">{{ $weibo->like_total }}</a>
											@else
											{{ $weibo->like_total }}
											@endif</td>
										<td class="center">{{ $weibo->updated_at }}</td>
										<td class="center"><a href="{{ url('admin/weibo/'.$weibo->id.'/edit') }}" class="btn btn-success">编辑</a></td>
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
</div>
@endsection

