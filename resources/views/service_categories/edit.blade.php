@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Service Category</h2>
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
    <form action="{{ route('serviceCategories.update',$service_category->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-12">
                <div class="float-right">
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Title:</strong>
                    <input type="text" name="title" value="{{ old('title',$service_category->title) }}" class="form-control" placeholder="Title">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label for="slug"><span style="color: red;">*</span><strong>SEO URL (Slug)</strong></label>
                    <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug', $service_category->slug ?? '') }}">
                    <small class="text-muted">
                        • Should be lowercase with hyphens instead of spaces (e.g., "my-service")<br>
                        • Avoid special characters and punctuation<br>
                        • Should be unique across all services
                    </small>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="meta_title"><span style="color: red;">*</span><strong>Meta Title</strong></label>
                    <input type="text" name="meta_title" id="meta_title" class="form-control" value="{{ old('meta_title', $service_category->meta_title ?? '') }}" maxlength="60">
                    <small class="text-muted">
                        • Recommended: 50-60 characters
                    </small>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="meta_description"><strong>Meta Description</strong></label>
                    <textarea name="meta_description" id="meta_description" class="form-control" rows="4" maxlength="160">{{ old('meta_description', $service_category->meta_description ?? '') }}</textarea>
                    <small class="text-muted">
                        • Recommended: 150-160 characters
                    </small>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                    <label for="meta_keywords"><strong>Meta Keywords</strong> (comma separated)</label>
                    <input type="text" name="meta_keywords" id="meta_keywords" class="form-control" value="{{ old('meta_keywords', $service_category->meta_keywords ?? '') }}" placeholder="keyword1, keyword2, keyword3">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="image">Upload Image</strong>
                    <input type="file" name="image" id="image" class="form-control-file">
                    <br>
                    <img id="preview" src="/service-category-images/{{$service_category->image}}" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong for="icon">Upload Icon</strong>
                    <input type="file" name="icon" id="icon" class="form-control-file">
                    <br>
                    <img id="icon-preview" src="/service-category-icons/{{$service_category->icon}}" height="130px">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Status:</strong>
                    <select name="status" class="form-control">
                        <option value="1" {{ old('status', $service_category->status ) == '1' ? 'selected' : '' }}>Enable</option>
                        <option value="0" {{ old('status', $service_category->status ) == '0' ? 'selected' : '' }}>Disable</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <span style="color: red;">*</span><strong>Description:</strong>
                    <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{$service_category->description}}</textarea>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Type:</strong>
                    <select name="type" class="form-control">
                        <option value="Male" {{ old('type', $service_category->type) === 'Male' ? 'selected' : '' }} >Male</option>
                        <option value="Female" {{ old('type', $service_category->type) === 'Female' ? 'selected' : '' }} >Female</option>
                        <option value="Both" {{ old('type', $service_category->type) === 'Both' ? 'selected' : '' }}>Both</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Parent Category:</strong>
                    <select name="parent_id" class="form-control">
                        <option></option>
                        @foreach($categories as $category)
                        @if($category->id != $service_category->id)
                        @if($category->id == $service_category->parent_id)
                        <option value="{{$category->id}}" selected>{{$category->title}}</option>
                        @else
                        <option value="{{$category->id}}" {{ old('parent_id') == $category->id ? 'selected' : '' }}>{{$category->title}}</option>
                        @endif
                        @endif
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group scroll-div">
                    <strong>Sub Category:</strong>
                    <input type="text" name="categories-search" id="categories-search" class="form-control" placeholder="Search Category By Name">
                    <table class="table table-striped table-bordered categories-table">
                        <tr>
                            <th></th>
                            <th>Name</th>
                        </tr>
                        @if(count($categories) > 0)

                        @php
                            // Use old input if available, otherwise use existing childCategoryIds
                            $selectedSubcategories = old('subcategoriesIds', $childCategoryIds ?? []);
                        @endphp
                        @foreach ($categories as $category)
                        @if($category->id != $service_category->id)

                        <tr>
                            <td>
                                <input type="checkbox" name="subcategoriesIds[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedSubcategories) ? 'checked' : '' }}>
                            </td>
                            <td>{{ $category->title }}</td>
                        </tr>
                        @endif
                        @endforeach
                        @else
                        <tr>
                            <td colspan="2" class="text-center">No categories found</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </div>
    </form>
</div>
<script>
    document.getElementById('image').addEventListener('change', function(e) {
        var preview = document.getElementById('preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
<script>
    document.getElementById('icon').addEventListener('change', function(e) {
        var preview = document.getElementById('icon-preview');
        preview.src = URL.createObjectURL(e.target.files[0]);
    });
</script>
<script>
    $(document).ready(function() {
        $("#categories-search").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".categories-table tr").hide();

            $(".categories-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();


                if (name.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
@endsection
