@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Update Staff Group</h2>
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
    <form action="{{ route('staffGroups.update',$staffGroup->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{old('name',$staffGroup->name)}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group scroll-div">
                    <span style="color: red;">*</span><strong>Staff Zones:</strong>
                    <input type="text" name="search-zone" id="search-zone" class="form-control" placeholder="Search Staff Zone By Name And Email">
                    <table class="table table-striped table-bordered staff-zone-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                        </tr>
                        @foreach ($staff_zones as $staff_zone)
                        <tr>
                            <td>
                                @if(in_array($staff_zone->id,$staff_zones_ids))
                                <input type="checkbox" name="staff_zone_ids[]" checked value="{{ $staff_zone->id }}" @if(in_array($staff_zone->id, old('staff_zone_ids', $staff_zones_ids))) checked @endif>
                                @else
                                <input type="checkbox" name="staff_zone_ids[]" value="{{ $staff_zone->id }}" @if(in_array($staff_zone->id, old('staff_zone_ids', $staff_zones_ids))) checked @endif>
                                @endif
                            </td>
                            <td>{{ $staff_zone->name }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group scroll-div">
                    <span style="color: red;">*</span><strong>Staffs:</strong>
                    <input type="text" name="search-staff" id="search-staff" class="form-control" placeholder="Search Staff By Name And Email">
                    <table class="table table-striped table-bordered staff-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        @foreach ($users as $staff)
                        @if($staff->hasRole("Staff"))
                        <tr>
                            <td>
                                @if(in_array($staff->id,$staff_ids))
                                <input type="checkbox" checked name="staffIds[]" value="{{ $staff->id }}" @if(in_array($staff->id, old('staffIds',$staff_ids))) checked @endif>
                                @else
                                <input type="checkbox" name="staffIds[]" value="{{ $staff->id }}" @if(in_array($staff->id, old('staffIds', $staff_ids))) checked @endif>
                                @endif
                            </td>
                            <td>{{ $staff->name }}</td>
                            <td>{{ $staff->email }}</td>
                        </tr>
                        @endif
                        @endforeach
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
    $(document).ready(function() {
        $("#search-staff").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".staff-table tr").hide();

            $(".staff-table tr").each(function() {

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

        $("#search-zone").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".staff-zone-table tr").hide();

            $(".staff-zone-table tr").each(function() {

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