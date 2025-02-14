@extends('layouts.app')
@section('content')
<div class="container">
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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="image">Type</strong>
                    <select name="type" class="form-control">
                        <option value="General" {{ old('type') == 'General' ? 'selected' : ''}}>General</option>
                        <option value="Specific" {{ old('type') == 'Specific' ? 'selected' : ''}}>Specific</option>
                        <option value="Partner" {{ old('type') == 'Partner' ? 'selected' : ''}}>Partner</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time Start:</strong>
                    <input type="time" name="time_start" value="{{ old('time_start') }}" class="form-control" placeholder="Time Start">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Time End:</strong>
                    <input type="time" name="time_end" value="{{ old('time_end') }}" class="form-control" placeholder="Time End">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>No. of Seats:</strong>
                    <input type="number" name="seat" value="{{ old('seat',1) }}" class="form-control">
                </div>
            </div>
            <div class="col-md-12" id="date" style="display: none;">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Date:</strong>
                    <input type="date" name="date" value="{{ old('date') }}" class="form-control" placeholder="Date">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Status:</strong>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status') == '1' ? 'selected' : ''}}> Enable</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : ''}}>Disable</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <span style="color: red;">*</span><strong for="image">Staff Group</strong>
                <select name="group_id" class="form-control">
                    <option></option>
                    @foreach($staff_groups as $staff_group )
                    <option value="{{$staff_group->id}}" >{{$staff_group->name}}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-12" id="group_staff" style="display: none;">
                <div class="form-group scroll-div">
                    <span style="color: red;">*</span><strong>Staff of Group:</strong>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search Staff By Name And Email">
                    <table class="table table-striped table-bordered">
                        <tr>
                            <th><input type="checkbox" checked onclick="$('input[name*=\'ids\']').prop('checked', this.checked);"></th>
                            <th>Name</th>
                            <th>Designation</th>
                            <th>Zone</th>
                            <th>Email</th>
                        </tr>
                        <tbody id="staff-container">

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
</div>
<script>
    $('select[name="type"]').on('change', function() {
        var type = $('select[name="type"]').val();
        if (type == 'Specific') {
            $('#date').show();
        } else if (type == 'General' || type == 'Partner') {
            $('#date').hide();
        }
    });


    $('select[name="group_id"]').on('change', function() {
        $('#group_staff').css('display', 'block')
        var group = $('select[name="group_id"]').val();

        $.ajax({
            url: '/staff-by-group',
            method: 'GET',
            cache: false,
            data: {
                group: group
            },
            success: function(response) {
                var staffs = response.staff;
                var allStaff = response.allStaff;

                var staffContainer = $('#staff-container');
                staffContainer.empty();
                var i = 1;

                allStaff.forEach(function(staff) {
                    var isChecked = staffs.some(function(selectedStaff) {
                        return selectedStaff.id === staff.id;
                    });

                    var checkedAttribute = isChecked ? 'checked' : '';

                    var html = '<tr><td><input type="checkbox" ' + checkedAttribute + ' name="ids[' + i + ']" value="' + staff.id + '"></td>'
                            + '<td>' + staff.name + '</td>'
                            + '<td>' + (staff.sub_title != null ? staff.sub_title : "") + '</td>'
                            + '<td>' + (staff.staffZones.length > 0 ?  staff.staffZones.join(', ') : '') + '</td>'
                            + '<td>' + staff.email + '</td></tr>';

                    staffContainer.append(html);
                    i++;
                });
            },
            error: function() {
                alert('Error retrieving staffs.');
            }
        });

    });

    $(document).ready(function() {
        $("#search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $("table tr").hide();

            $("table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
@endsection