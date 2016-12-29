@extends('admin/app')

@section('content')
<div class="wrapper wrapper-content animated fadeInRight">
	    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-body">{{ $notice }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
