@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Update Time Slot</h2>
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
    <form action="{{ route('timeSlots.update',$time_slot->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{$time_slot->name}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="image">Type</strong>
                    <select name="type" class="form-control">
                        @if($time_slot->type == "General")
                            <option value="General" selected>General</option>
                            <option value="Specific">Specific</option>
                        @elseif($time_slot->type == "Specific")
                            <option value="General">General</option>
                            <option value="Specific" selected>Specific</option>
                        @endif
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time Start:</strong>
                    <input type="time" name="time_start" value="{{$time_slot->time_start}}" class="form-control" placeholder="Time Start">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time End:</strong>
                    <input type="time" name="time_end" value="{{$time_slot->time_end}}" class="form-control" placeholder="Time End">
                </div>
            </div>
            <div class="col-md-12" id="date" style="display: none;">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date:</strong>
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
                    @endif        
                </select>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Space Availability:</strong>
                    <input type="text" name="space_availability" value="{{$time_slot->space_availability}}" class="form-control" placeholder="Space">
                </div>
            </div>
            <div class="form-group">
                <span style="color: red;">*</span><strong for="image">Staff Group</strong>
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
            <div class="col-md-12" id="group_staff" style="display: none;">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Staff of Group:</strong>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search Staff By Name And Email">
                    <table class="table table-bordered">
                        <tr>
                            <th><input type="checkbox" onclick="$('input[name*=\'ids\']').prop('checked', this.checked);"></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        <tbody id="staff-container">
                        @foreach ($staffs as $staff)
                        @if($staff->getRoleNames() == '["Staff"]')
                        @if(in_array($staff->id,$group_staff))
                            <tr>
                                <td>
                                    @if(isset($time_slot->available_staff))
                                        @if(in_array($staff->id,unserialize($time_slot->available_staff)))
                                            <input type="checkbox" checked name="ids[{{ ++$i }}]" value="{{ $staff->id }}">
                                        @else
                                            <input type="checkbox" name="ids[{{ ++$i }}]" value="{{ $staff->id }}">
                                        @endif
                                    @else
                                        <input type="checkbox" name="ids[{{ ++$i }}]" value="{{ $staff->id }}">
                                    @endif
                                </td>
                                <td>{{ $staff->name }}</td>
                                <td>{{ $staff->email }}</td>
                            </tr>
                        @endif
                        @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
<script>
    $('select[name="type"]').on('change', function() {
        var type = $('select[name="type"]').val();
        if(type == 'Specific'){
            $('#date').show();
        }else if(type == 'General'){
            $('#date').hide();
        }
    });

    $(document).ready(function() {
        var type =  '{{$time_slot->type}}';
        if(type == 'Specific'){
            $('#date').show();
        }else if(type == 'General'){
            $('#date').hide();
        }
    });

    $(document).ready(function() {
        var group_id =  '{{$time_slot->group_id}}';
        if(group_id == ' '){
            $('#group_staff').hide();
        }else{
            $('#group_staff').show();
        }
    });


    $('select[name="group_id"]').on('change', function() {
        $('#group_staff').css('display','block')
        var group = $('select[name="group_id"]').val();
        
        $.ajax({
                url: '/staff-by-group',
                method: 'GET',
                data: {
                    group: group
                },
                success: function(response) {
                    var staffs = response;
                    
                    var staffContainer = $('#staff-container');
                    staffContainer.empty();
                    var i = 1;

                    staffs.forEach(function(staff) {
                        var html = '<tr><td><input type="checkbox" checked name="ids[' + i + ']" value="' + staff[0].id + '"></td><td>' + staff[0].name + '</td><td>' + staff[0].email + '</td></tr>';
                        staffContainer.append(html);
                        i++;
                    });
                },
                error: function() {
                    alert('Error retrieving staffs.');
                }
            });
    });
    
    $(document).ready(function(){
    $("#search").keyup(function(){
        var value = $(this).val().toLowerCase();
        
        $("table tr").hide();

        $("table tr").each(function() {

            $row = $(this);

            var name = $row.find("td:first").next().text().toLowerCase();

            var email = $row.find("td:last").text().toLowerCase();

            if (name.indexOf(value) != -1) {
                $(this).show();
            }else if(email.indexOf(value) != -1) {
                $(this).show();
            }
        });
    });
});
</script>
@endsection