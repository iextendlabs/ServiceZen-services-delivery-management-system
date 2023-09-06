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
                <strong>Staff Zone</strong><br><br>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                    </tr>
                    @if(isset($staffGroup->staffZones))
                        @foreach($staffGroup->staffZones as $staff_zone) 
                    <tr>
                        <td>{{ $staff_zone->id }}</td>
                        <td>{{ $staff_zone->name }}</td>
                    </tr>
                    @endforeach
                    @endif

                </table>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Staff</strong><br><br>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                    </tr>
                    @if(isset($staffGroup->staffs))
                        @foreach($staffGroup->staffs as $staff) 
                    <tr>
                        <td>{{ $staff->id }}</td>
                        <td>{{ $staff->name }}</td>
                    </tr>
                    @endforeach
                    @endif

                </table>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Driver</strong><br><br>
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Id</th>
                        <th>Name</th>
                    </tr>
                    @if(isset($staffGroup->drivers))
                        @foreach($staffGroup->drivers as $driver) 
                    <tr>
                        <td>{{ $driver->id }}</td>
                        <td>{{ $driver->name }}</td>
                    </tr>
                    @endforeach
                    @endif

                </table>
            </div>
        </div>
    </div>
@endsection