@extends('layouts/app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="alert alert-danger">
              <ul>
                  <li>{{ $error }}</li>
              </ul>
            </div>
        </div>
    </div>
</div>
@endsection
