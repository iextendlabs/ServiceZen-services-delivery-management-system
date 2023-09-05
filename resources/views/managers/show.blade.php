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
            <strong>Supervisor and Staff:</strong>
            <table class="table table-striped table-bordered album bg-light">
                <tr>
                    <th>Sr#</th>
                    <th>Supervisor Name</th>
                    <th>Sr#</th>
                    <th>Staff Name</th>
                </tr>
                @if(count($manager->managerSupervisors))
                @foreach ($manager->managerSupervisors as $key=>$supervisor)
                <tr>
                    <td rowspan="{{ count($supervisor->supervisor->staffSupervisors) + 1 }}">{{ ++$key }}</td>
                    <td rowspan="{{ count($supervisor->supervisor->staffSupervisors) + 1 }}">{{ $supervisor->supervisor->name }}</td>
                </tr>
                @if(count($supervisor->supervisor->staffSupervisors))
                @foreach ($supervisor->supervisor->staffSupervisors as $key=>$staff)
                <tr>
                    <td>{{ ++$key }}</td>
                    <td>{{ $staff->email }}</td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td>{{ ++$key }}</td>
                    <td colspan="3">No staff members.</td>
                </tr>
                @endif
                @endforeach
                @else
                <tr>
                    <td colspan="5">There are no supervisors.</td>
                </tr>
                @endif
            </table>
        </div>
    </div>


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