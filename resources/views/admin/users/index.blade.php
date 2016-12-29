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
				<li class="active"><strong>用户数据</strong></li>
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
                            <a class="close-link">
                                <i class="fa fa-times"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="">
                            <a href="{{ url($path) }}/create" class="btn btn-primary ">新增</a>
                            <a href="{{ url('admin/authorizeCn/test') }}" class="btn btn-primary ">测试weibo.cn授权</a>
                        </div>
                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
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
										<th class="sorting_asc" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 180px;" >微博名</th>
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 100px;" aria-label="粉丝：激活排序列升序">粉丝</th>
											
										<!--<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 120px;">关注</th>  -->
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 80px;" aria-label="微博数：激活排序列升序">微博数</th>
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 100px;">认证</th>
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 100px;">介绍</th>
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 100px;">位置</th>
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 160px;" aria-label="更新时间：激活排序列升序">更新时间</th>
										<th class="sorting" tabindex="0" aria-controls="editable" rowspan="1" colspan="1" style="width: 214px;">操作</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($weibos as $weibo)
									<tr class="gradeA odd">

										<td class="sorting_1"><a href="http://weibo.cn/{{ $weibo->uid }}" target="_blank">{{ $weibo->username }}</a></td>
										<td class="center">{{ $weibo->fans }}</td>
<!-- 										<td class="center ">{{ $weibo->follow }}</td> -->
										<td class="center ">{{ $weibo->weibos }}</td>
										<td class="center ">{{ $weibo->type }}</td>
										<td class="center ">{{ $weibo->intro }}</td>
										<td class="center ">{{ $weibo->place }}</td>
										<td class="center ">{{ $weibo->updated_at }}</td>
										<td class="center "><a href="{{ url($path) }}/{{ $weibo->uid }}/edit"class="btn btn-success">编辑</a> 
										@if($weibo->fans > 0) <a href="{{ url('admin/fans/'.$weibo->uid) }}" class="btn btn-danger">粉丝</a> @endif</td>
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

