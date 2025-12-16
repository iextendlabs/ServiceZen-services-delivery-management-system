@extends('layouts.app')
@section('content')
<div class="container-fluid px-1">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h2 class="m-0 h5"><strong>Show Supervisor</strong></h2>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                {{ $supervisor->name }}
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                {{ $supervisor->email }}
                            </div>
                        </div>
                        @if($supervisor->SupervisorToManager)
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Manager:</strong>
                                {{ $supervisor->SupervisorToManager->manager->name }}
                            </div>
                        </div>
                        @endif
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Staff:</strong>
                                <table class="table table-striped table-bordered album bg-light">
                                    <tr>
                                        <th>Sr#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                    </tr>
                                    @if(count($supervisor->staffSupervisors))

                                    @foreach ($supervisor->staffSupervisors as $key=>$staff)
                                    <tr>
                                        <td>{{ ++$key }}</td>
                                        <td>{{ $staff->name }}</td>
                                        <td>{{ $staff->email }}</td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="3">There is no staff.</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Roles:</strong>
                                @if(!empty($supervisor->getRoleNames()))
                                @foreach($supervisor->getRoleNames() as $v)
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