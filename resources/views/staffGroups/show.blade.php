@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="pull-left">
                <h2> Show Staff Group</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $staffGroup->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff Zone:</strong>
                {{ $staffGroup->staffZone->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff</strong><br><br>
                <table class="table table-bordered">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Phone</th>
                    </tr>
                    @foreach($users as $user)
                    @if(in_array($user->id, unserialize($staffGroup->staff_ids))) 
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->staff->phone}}</td>
                    </tr>
                    @endif
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection