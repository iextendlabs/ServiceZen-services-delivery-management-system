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
            <div class="form-group">
                <strong for="image">Type</strong>
                <select name="type" class="form-control">
                    @if($time_slot->type == "General")
                        <option value="General" selected>General</option>
                        <option value="Specific">Specific</option>
                    @elseif($time_slot->type == "Specific")
                        <option value="General">General</option>
                        <option value="Specific" selected>Specific</option>
                    @else
                        <option value="General">General</option>
                        <option value="Specific">Specific</option>
                    @endif
                </select>
            </div>
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
            <div class="col-md-12" id="date" style="display: none;">
                <div class="form-group">
                    <strong>Date:</strong>
                    <input type="date" name="date" value="{{$time_slot->date}}" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="form-group">
                <strong for="image">Active</strong>
                <select name="active" class="form-control">
                    @if($time_slot->active == "Available")
                        <option value="Available" selected>Available</option>
                        <option value="Unavailable">Unavailable</option>
                    @elseif($time_slot->active == "Unavailable")
                        <option value="Available">Available</option>
                        <option value="Unavailable" selected>Unavailable</option>
                    @else
                        <option value="Available">Available</option>
                        <option value="Unavailable">Unavailable</option>
                    @endif
                </select>
            </div>
            <div class="form-group">
                <strong for="image">Staff Group</strong>
                <select name="group_id" class="form-control">
                    <option></option>
                    @foreach($staff_groups as $staff_group )
                    @if($time_slot->group_id == $staff_group->id)
                        <option value="{{$staff_group->id}}" selected>{{$staff_group->name}}</option>
                    @else
                        <option value="{{$staff_group->id}}">{{$staff_group->name}}</option>
                    @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <script>
    $(document).ready(function() {
        $('select[name="type"]').on('change', function() {
            var type = $('select[name="type"]').val();
            if(type == 'Specific'){
                $('#date').show();
            }else if(type == 'General'){
                $('#date').hide();
            }
        });
    });

    $(document).ready(function() {
        var type =  '{{$time_slot->type}}';
        if(type == 'Specific'){
            $('#date').show();
        }else if(type == 'General'){
            $('#date').hide();
        }
    });
    </script>
@endsection