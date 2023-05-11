@extends('layouts.app')
@section('content')
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Service</h2>
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
    <form action="{{ route('services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="id" value="{{$service->id}}">
         <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    <input type="text" name="name" value="{{$service->name}}" class="form-control" placeholder="Name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="image">Upload Image</strong>
                    <input type="file" name="image" id="image" class="form-control-file ">
                    <br>
                    <img id="preview" src="/service-images/{{$service->image}}" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Description:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{$service->description}}</textarea>
                    <script>
                        CKEDITOR.replace( 'description' );
                    </script>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Short Description:</strong>
                    <textarea class="form-control" style="height:150px" name="short_description" placeholder="Short Description">{{$service->short_description}}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Price:</strong>
                    <input type="number" value="{{$service->price}}" name="price" class="form-control" placeholder="Price">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Duration:</strong>
                    <input type="text" value="{{$service->duration}}" name="duration" class="form-control" placeholder="Duration">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Category:</strong>
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
                    <table class="table table-bordered">
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
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection