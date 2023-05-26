@extends('layouts.app')
@section('content')
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Update Service</h2>
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
    <form action="{{ route('services.update',$service->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Name:</strong>
                    <input type="text" name="name" value="{{$service->name}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong for="image">Upload Image</strong>
                    <input type="file" name="image" id="image" class="form-control-file ">
                    <br>
                    <img id="preview" src="/service-images/{{$service->image}}" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Description:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{$service->description}}</textarea>
                    <script>
                        CKEDITOR.replace( 'description' );
                    </script>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Short Description:</strong>
                    <textarea class="form-control" style="height:150px" name="short_description" placeholder="Short Description">{{$service->short_description}}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Price:</strong>
                    <input type="number" value="{{$service->price}}" name="price" class="form-control" placeholder="Price">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Discount Price:</strong>
                    <input type="number" value="{{$service->discount}}" name="discount" class="form-control" placeholder="Discount Price">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Duration:</strong>
                    <input type="text" value="{{$service->duration}}" name="duration" class="form-control" placeholder="Duration">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Category:</strong>
                    <select name="category_id" class="form-control">
                        <option></option>
                        @foreach($service_categories as $category)
                        @if($category->id == $service->category_id)
                            <option value="{{$category->id}}" selected>{{$category->title}}</option>
                        @else
                            <option value="{{$category->id}}">{{$category->title}}</option>
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Package Services:</strong>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Search Services By Name And Price">
                    <table class="table table-bordered services-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Price</th>
                        </tr>
                        @foreach ($all_services as $service)
                        <tr>
                            <td>
                                @if(in_array($service->id,$package_services))
                                <input type="checkbox" checked name="packageId[{{ ++$i }}]" value="{{ $service->id }}">
                                @else
                                <input type="checkbox" name="packageId[{{ ++$i }}]" value="{{ $service->id }}">
                                @endif
                            </td>
                            <td>{{ $service->name }}</td>
                            <td>{{ $service->price }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
            <hr>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Note:</strong>
                    <textarea class="form-control" style="height:150px" name="note" placeholder="Note">@if(isset($userNote)){{$userNote->note}}@endif</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Select User For Note:</strong>
                    <input type="text" name="search_user" id="search-user" class="form-control" placeholder="Search User By Name And Price">
                    <table class="table table-bordered user-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        @foreach ($users as $user)
                        <tr>
                            <td>
                                @if(isset($userNote))
                                @if(in_array($user->id,unserialize($userNote->user_ids)))
                                    <input type="checkbox" checked name="userIds[{{ ++$i }}]" value="{{ $user->id }}">
                                    @else
                                    <input type="checkbox" name="userIds[{{ ++$i }}]" value="{{ $user->id }}">
                                    @endif
                                @else
                                <input type="checkbox" name="userIds[{{ ++$i }}]" value="{{ $user->id }}">
                                @endif
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                        </tr>
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
        
        $(".services-table tr").hide();

        $(".services-table tr").each(function() {

            $row = $(this);
            
            var name = $row.find("td:first").next().text().toLowerCase();

            var price = $row.find("td:last").text().toLowerCase();

            if (name.indexOf(value) != -1) {
                $(this).show();
            }else if(price.indexOf(value) != -1) {
                $(this).show();
            }
        });
    });
});
</script>
<script>
$(document).ready(function(){
    $("#search-user").keyup(function(){
        var value = $(this).val().toLowerCase();
        
        $(".user-table tr").hide();

        $(".user-table tr").each(function() {

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
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection