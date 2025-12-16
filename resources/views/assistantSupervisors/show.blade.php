@extends('layouts.app')
@section('content')
@section('page_title')
    <h3> Show Assistant Supervisor </h3>
@endsection
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="m-0 h5"><strong> Show Assistant Supervisor</strong></h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {{ $assistant_supervisor->name }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                {{ $assistant_supervisor->email }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Roles:</strong>
                                @if(!empty($assistant_supervisor->getRoleNames()))
                                @foreach($assistant_supervisor->getRoleNames() as $v)
                                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection