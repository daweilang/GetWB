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
		<div class="search-form">
			<div class="col-sm-6">
				<form action="{{ url('admin/users') }}" method="get">
					<div class="input-group">
						<input placeholder="按uid查找用户" name="uid" class="form-control input-lg" type="text" value='{{ $uid }}'>
						<div class="input-group-btn">
							<button class="btn btn-lg btn-primary" type="submit">搜索</button>
						</div>
					</div>
				</form>
				<br>
        	</div>
        </div>
		<div class="row">
            <div class="col-sm-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>微博用户列表</h5>
                    </div>
                    <div class="ibox-content">
                        <div id="editable_wrapper" class="dataTables_wrapper form-inline" role="grid">
						<div class="row">
							<table class="table table-striped table-bordered table-hover  dataTable" id="editable" aria-describedby="editable_info">
								<thead>
									<tr role="row">
										<th class="sorting_asc" tabindex="0" rowspan="1" colspan="1" style="width: 180px;" >微博名</th>										
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;">uid</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;">粉丝</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 80px;">微博数</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;">介绍</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 100px;">位置</th>
										<th class="sorting" tabindex="0" rowspan="1" colspan="1" style="width: 160px;">更新时间</th>
									</tr>
								</thead>
								<tbody>

									@foreach ($weibos as $weibo)
									<tr class="gradeA odd">
										<td class="sorting_1"><a href="http://weibo.com/u/{{ $weibo->uid }}" target="_blank">{{ $weibo->username }}</a></td>
										<td class="center">{{ $weibo->uid }}</td>
										<td class="center">{{ $weibo->fans }}</td>
<!-- 									<td class="center ">{{ $weibo->follow }}</td> -->
										<td class="center ">{{ $weibo->weibos }}</td>
										<td class="center ">{{ $weibo->intro }}</td>
										<td class="center ">{{ $weibo->place }}</td>
										<td class="center ">{{ $weibo->updated_at }}</td>
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

