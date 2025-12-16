@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between">
                <h2 class="mb-0">Show User</h2>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="form-row">
                <div class="form-group col-12 col-md-4">
                    <strong>Name:</strong>
                    <div>{{ $user->name }}</div>
                </div>

                <div class="form-group col-12 col-md-4">
                    <strong>Email:</strong>
                    <div>{{ $user->email }}</div>
                </div>

                <div class="form-group col-12 col-md-4">
                    <strong>Roles:</strong>
                    <div>
                        @if(!empty($user->getRoleNames()))
                            @foreach($user->getRoleNames() as $v)
                                <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection