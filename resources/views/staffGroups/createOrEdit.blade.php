@extends('layouts.app')
@section('content')
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Staff Group</h2>
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
    <form action="{{ route('staffGroups.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$staffGroup->id}}">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{$staffGroup->name}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Staffs:</strong>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search Staff By Name And Email">
                    <table class="table table-bordered">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        @foreach ($staffs as $staff)
                        @if($staff->getRoleNames() == '["Staff"]')
                        <tr>
                            <td>
                                @if(isset($staffGroup->staff_ids))
                                    @if(in_array($staff->id,unserialize($staffGroup->staff_ids)))
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
                        @endforeach
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
    </form>
    <script>
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