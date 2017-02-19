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
				<li><a>数据统计</a></li>
				<li class="active"><strong>微博数据</strong></li>
			</ol>
		</div>
		<div class="col-lg-2"></div>
	</div>

	<div class="wrapper wrapper-content animated fadeInRight">

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
										<th class="sorting_asc" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 200px;"
											aria-sort="ascending" aria-label="任务名：激活排序列升序">任务名</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 100px;"
											aria-label="任务状态：激活排序列升序">任务状态</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 120px;">微博题目</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 100px;"
											aria-label="评论数：激活排序列升序">评论页数</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 100px;"
											aria-label="评论数：激活排序列升序">评论数</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 160px;"
											aria-label="更新时间：激活排序列升序">更新时间</th>
										<th class="sorting" tabindex="0" aria-controls="editable"
											rowspan="1" colspan="1" style="width: 214px;">操作</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($weibos as $weibo)
									<tr class="gradeA odd">

										<td class="sorting_1"><a href="{{ $weibo->wb_url }}"
											target="_blank">{{ $weibo->wb_name }}</a></td>
										<td class="center">{{ $weibo->wb_status }}</td>
										<td class="center ">{{ $weibo->wb_title }}</td>
										<td class="center ">{{ $weibo->wb_comment_page }}</td>
										<td class="center ">{{ $weibo->wb_comment_total }}</td>
										<td class="center ">{{ $weibo->updated_at }}</td>
										<td class="center ">
											<a href="{{ url('admin/weibo/'.$weibo->id.'/edit') }}" class="btn btn-success">编辑</a> @if($weibo->wb_mid) 
											<a href="{{ url('admin/commentJob/'.$weibo->mid) }}" class="btn btn-danger">评论详情</a> @endif</td>
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

