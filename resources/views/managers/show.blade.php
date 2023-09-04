@extends('layouts.app')
@section('content')
<div class="row">
    <div class="col-md-12 margin-tb">
        <div class="float-start">
            <h2> Show Manager</h2>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="form-group">
            <strong>Name:</strong>
            {{ $manager->name }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Email:</strong>
            {{ $manager->email }}
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group">
            <strong>Supervisor:</strong>
            <table class="table table-striped table-bordered album bg-light">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                @if(count($manager->managerSupervisors))
                @foreach ($manager->managerSupervisors as $key=>$supervisor)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $supervisor->supervisor->name }}</td>
                    <td>{{ $supervisor->supervisor->email }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="3">There is no Supervisor.</td>
                </tr>
                @endif
            </table>
        </div>
    </div>
    @if($manager->managerSupervisors)
    <div class="col-md-12">
        <div class="form-group">
            <strong>Staff:</strong>
            <table class="table table-striped table-bordered album bg-light">
                <tr>
                    <th>Sr#</th>
                    <th>Name</th>
                    <th>Email</th>
                </tr>
                @foreach ($manager->managerSupervisors as $key=>$supervisor)
                @foreach ($supervisor->staffSupervisor as $key=>$staff)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $staff->user->name }}</td>
                    <td>{{ $staff->user->email }}</td>
                </tr>
                @endforeach
                
                @endforeach
            </table>
        </div>
    </div>
    @endif
    <div class="col-md-12">
        <div class="form-group">
            <strong>Roles:</strong>
            @if(!empty($manager->getRoleNames()))
            @foreach($manager->getRoleNames() as $v)
            <span class="badge rounded-pill bg-dark">{{ $v }}</span>
            @endforeach
            @endif
        </div>
    </div>
</div>
@endsection