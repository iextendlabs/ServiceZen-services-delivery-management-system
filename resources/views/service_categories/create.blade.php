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
        <form action="{{ route('serviceCategories.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-3" id="categoryTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab"
                        aria-controls="general" aria-selected="true">General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="seo-tab" data-toggle="tab" href="#seo" role="tab" aria-controls="seo"
                        aria-selected="false">SEO</a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content" id="categoryTabContent">
                <!-- General Tab -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Title:</strong>
                                <input type="text" name="title" value="{{ old('title') }}" class="form-control"
                                    placeholder="Title">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Description:</strong>
                                <textarea class="form-control" style="height:150px" name="description" placeholder="Description">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <!-- Upload Image -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong for="image">Upload Image</strong>
                                <input type="file" name="image" id="image" class="form-control-file">
                                <br>
                                <img id="preview" src="/service-category-images/" height="130px">
                            </div>
                        </div>

                        <!-- Upload Icon -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong for="icon">Upload Icon</strong>
                                <input type="file" name="icon" id="icon" class="form-control-file">
                                <br>
                                <img id="icon-preview" src="/service-category-icons/" height="130px">
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                    <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                                </select>
                            </div>
                        </div>

                        <!-- Feature Category -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Feature Category:</strong>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="feature" id="feature"
                                        value="1" {{ old('feature') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="feature">Enable featured category</label>
                                </div>
                            </div>
                        </div>

                        <!-- Feature on Bottom -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Feature on Bottom:</strong>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="feature_on_bottom"
                                        id="feature_on_bottom" value="1"
                                        {{ old('feature_on_bottom') == '1' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="feature_on_bottom">Show at bottom section</label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Sort Order:</strong>
                                <input type="number" name="sort" class="form-control"
                                    value="{{ old('sort') }}">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>

                        <!-- Type -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Type:</strong>
                                <select name="type" class="form-control">
                                    <option value="Male" {{ old('type') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('type') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Both" {{ old('type') == 'Both' ? 'selected' : '' }}>Both</option>
                                </select>
                            </div>
                        </div>

                        <!-- Parent Category -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Parent Category:</strong>
                                <select name="parent_id" class="form-control">
                                    <option></option>
                                    @foreach ($service_categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ old('parent_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Sub Category -->
                        <div class="col-md-12">
                            <div class="form-group scroll-div">
                                <strong>Sub Category:</strong>
                                <input type="text" name="categories-search" id="categories-search"
                                    class="form-control" placeholder="Search Category By Name">
                                <table class="table table-striped table-bordered categories-table">
                                    <tr>
                                        <th></th>
                                        <th>Name</th>
                                    </tr>
                                    @if (count($service_categories) > 0)
                                        @foreach ($service_categories as $category)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="subcategoriesIds[]"
                                                        value="{{ $category->id }}"
                                                        {{ in_array($category->id, old('subcategoriesIds', [])) ? 'checked' : '' }}>
                                                </td>
                                                <td>{{ $category->title }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="2" class="text-center">No categories found</td>
                                        </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Tab -->
                <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                    <div class="row">
                        <!-- Slug -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="slug"><span style="color: red;">*</span><strong>SEO URL
                                        (Slug)</strong></label>
                                <input type="text" name="slug" id="slug" class="form-control"
                                    value="{{ old('slug') }}">
                                <small class="text-muted">
                                    • Lowercase with hyphens (e.g., "my-service")<br>
                                    • Avoid special characters<br>
                                    • Should be unique
                                </small>
                            </div>
                        </div>

                        <!-- Meta Title -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_title"><span style="color: red;">*</span><strong>Meta
                                        Title</strong></label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control"
                                    value="{{ old('meta_title') }}" maxlength="60">
                                <small class="text-muted">• Recommended: 50-60 characters</small>
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_description"><strong>Meta Description</strong></label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="4" maxlength="160">{{ old('meta_description') }}</textarea>
                                <small class="text-muted">• Recommended: 150-160 characters</small>
                            </div>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_keywords"><strong>Meta Keywords</strong> (comma separated)</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                                    value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-primary">Submit</button>
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
