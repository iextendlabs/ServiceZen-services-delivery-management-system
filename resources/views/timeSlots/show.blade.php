@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2> Show Time Slot</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>Name:</strong>
                {{ $time_slot->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Type:</strong>
                {{ $time_slot->type }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Time Start:</strong>
                {{ date('h:i A', strtotime($time_slot->time_start)) }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Time End:</strong>
                {{ date('h:i A', strtotime($time_slot->time_end)) }}
            </div>
        </div>
        @if($time_slot->type == 'Specific')
        <div class="col-md-12">
            <div class="form-group">
                <strong>Date:</strong>
                {{ $time_slot->date }}
            </div>
        </div>
        @endif
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff Group:</strong>
                {{ $time_slot->group->name }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Available Staff of Group</strong><br><br>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Phone</th>
                    </tr>
                    @foreach($staffs as $staff)
                    <tr>
                        <td>{{ $staff->id }}</td>
                        <td>{{ $staff->name }}</td>
                        <td>{{ $staff->staff->phone}}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
@endsection