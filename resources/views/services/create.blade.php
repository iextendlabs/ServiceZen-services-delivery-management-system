@extends('layouts.app')
@section('content')
<script src="https://cdn.ckeditor.com/4.21.0/standard/ckeditor.js"></script>
<div class="row">
    <div class="col-md-6 margin-tb">
        <h2>Add New Service</h2>
    </div>
    <div class="col-md-6">
        <button type="submit" form="services-form" class="btn btn-primary float-end">Save</button>
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
<form action="{{ route('services.store') }}" method="POST" id="services-form" enctype="multipart/form-data">
    @csrf
    <ul class="nav nav-tabs" id="myTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="package-services-tab" data-toggle="tab" href="#package-services" role="tab" aria-controls="package-services" aria-selected="false">Package Services</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="add-ons-tab" data-toggle="tab" href="#add-ons" role="tab" aria-controls="add-ons" aria-selected="false">Add ONs</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="variant-tab" data-toggle="tab" href="#variant" role="tab" aria-controls="variant" aria-selected="false">Variant Services</a>
        </li>
    </ul>
    <div class="tab-content" id="myTabsContent">
        <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{old('name') }}" class="form-control" placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong for="image">Upload Image</strong>
                        <input type="file" name="image" id="image" class="form-control-file ">
                        <br>
                        <img id="preview" src="/service-images/" height="130px">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Description:</strong>
                        <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{old('description') }}</textarea>
                        <script>
                            CKEDITOR.replace('description');
                        </script>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Short Description:</strong>
                        <textarea class="form-control" style="height:150px" name="short_description" placeholder="Short Description">{{old('short_description') }}</textarea>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Price:</strong>
                        <input type="number" value="{{old('price') }}" name="price" class="form-control" placeholder="Price">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Discount Price:</strong>
                        <input type="number" value="{{old('discount') }}" name="discount" class="form-control" placeholder="Discount Price">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Duration:</strong>
                        <input type="text" value="{{old('duration') }}" name="duration" class="form-control" placeholder="Duration">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Category:</strong>
                        <select name="category_id" class="form-control">
                            <option></option>
                            @foreach($service_categories as $category)
                            <option value="{{$category->id}}">{{$category->title}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="package-services" role="tabpanel" aria-labelledby="package-services-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Package Services:</strong>
                        <input type="text" name="package-search" id="package-search" class="form-control" placeholder="Search Services By Name And Price">
                        <table class="table table-striped table-bordered services-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                            @foreach ($all_services as $service)
                            <tr>
                                <td>
                                    <input type="checkbox" name="packageId[{{ ++$i }}]" value="{{ $service->id }}">
                                </td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->price }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="add-ons" role="tabpanel" aria-labelledby="add-ons-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Add ONs Services:</strong>
                        <input type="text" name="add-ons-search" id="add-ons-search" class="form-control" placeholder="Search Services By Name And Price">
                        <table class="table table-striped table-bordered add-ons-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                            @foreach ($all_services as $service)
                            <tr>
                                <td>
                                    <input type="checkbox" name="addONsId[{{ ++$i }}]" value="{{ $service->id }}">
                                </td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->price }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="variant" role="tabpanel" aria-labelledby="variant-tab">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Add ONs Services:</strong>
                        <input type="text" name="variant-search" id="variant-search" class="form-control" placeholder="Search Services By Name And Price">
                        <table class="table table-striped table-bordered variant-table">
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Price</th>
                            </tr>
                            @foreach ($all_services as $service)
                            <tr>
                                <td>
                                    <input type="checkbox" name="variantId[{{ ++$i }}]" value="{{ $service->id }}">
                                </td>
                                <td>{{ $service->name }}</td>
                                <td>{{ $service->price }}</td>
                            </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <!-- <div class="col-md-12">
                <div class="form-group">
                    <strong>Note:</strong>
                    <textarea class="form-control" style="height:150px" name="note" placeholder="Note">{{old('note') }}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Select User For Note:</strong>
                    <input type="text" name="search_user" id="search-user" class="form-control" placeholder="Search User By Name And Price">
                    <table class="table table-striped table-bordered user-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                        @foreach ($users as $user)
                        @if($user->getRoleNames() == '["Customer"]')
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
                        @endif
                        @endforeach
                    </table>
                </div>
            </div> -->

        </div>
        <div class="col-md-12 text-center">
            <button type="submit" class="btn btn-block btn-primary">Save</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        $("#package-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".services-table tr").hide();

            $(".services-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var price = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (price.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

        $("#add-ons-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".add-ons-table tr").hide();

            $(".add-ons-table tr").each(function() {

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

        $("#variant-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".variant-table tr").hide();

            $(".variant-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var price = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (price.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });

        $("#search-user").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".user-table tr").hide();

            $(".user-table tr").each(function() {

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
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
@endsection