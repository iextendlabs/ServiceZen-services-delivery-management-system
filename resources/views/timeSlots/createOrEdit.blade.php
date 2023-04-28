@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Time Slot</h2>
        </div>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('timeSlots.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$time_slot->id}}">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Time Start:</strong>
                    <input type="time" name="time_start" value="{{$time_slot->time_start}}" class="form-control" placeholder="Time Start">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Time End:</strong>
                    <input type="time" name="time_end" value="{{$time_slot->time_end}}" class="form-control" placeholder="Time End">
                </div>
            </div>
            <div class="form-group">
                <strong for="image">Active</strong>
                <select name="active" class="form-control">
                    <option></option>
                    @if($time_slot->active == "True")
                        <option value="True" selected>True</option>
                        <option value="False">False</option>
                    @elseif($time_slot->active == "False")
                        <option value="True">True</option>
                        <option value="False" selected>False</option>
                    @else
                        <option value="True">True</option>
                        <option value="False">False</option>
                    @endif
                </select>
            </div>
            <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
@endsection