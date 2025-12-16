@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Update Service Category</h2>
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
        <form action="{{ route('serviceCategories.update', $service_category->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" name="url" value="{{ url()->previous() }}">

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-3" id="categoryTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="general-tab" data-bs-toggle="tab" href="#general" role="tab"
                        aria-controls="general" aria-selected="true">General</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="seo-tab" data-bs-toggle="tab" href="#seo" role="tab" aria-controls="seo"
                        aria-selected="false">SEO</a>
                </li>
            </ul>

            <!-- Tab Contents -->
            <div class="tab-content" id="categoryTabContent">
                <!-- GENERAL TAB -->
                <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label"><span class="text-danger">*</span> Title</label>
                                <input type="text" name="title" value="{{ old('title', $service_category->title) }}" class="form-control rounded-3" placeholder="Title">
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label"><span class="text-danger">*</span> Description</label>
                                <textarea class="form-control rounded-3" style="height:150px" name="description" placeholder="Description">{{ old('description', $service_category->description) }}</textarea>
                            </div>
                        </div>

                        <!-- Image -->
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Upload Image <small class="text-muted">(318 x 192px)</small></label>
                                <input type="file" name="image" id="image" class="form-control">
                                <div class="mt-2">
                                    <img id="preview" src="/service-category-images/{{ $service_category->image }}" class="img-fluid rounded" style="max-height:120px">
                                </div>
                            </div>
                        </div>

                        <!-- Icon -->
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Upload Icon</label>
                                <input type="file" name="icon" id="icon" class="form-control">
                                <div class="mt-2">
                                    <img id="icon-preview" src="/service-category-icons/{{ $service_category->icon }}" class="img-fluid rounded" style="max-height:120px">
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="1" {{ old('status', $service_category->status) == '1' ? 'selected' : '' }}>Enable</option>
                                    <option value="0" {{ old('status', $service_category->status) == '0' ? 'selected' : '' }}>Disable</option>
                                </select>
                            </div>
                        </div>

                        <!-- Feature Category -->
                        <div class="col-12 col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input type="hidden" name="feature" value="0">
                                <input class="form-check-input" type="checkbox" name="feature" id="feature" value="1" {{ old('feature', $service_category->feature) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="feature">Enable featured category</label>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort" class="form-control rounded-3" value="{{ old('sort', $service_category->sort ?? 0) }}">
                                <div class="form-text">Lower numbers appear first</div>
                            </div>
                        </div>
                        <!-- Feature on Bottom -->
                        <div class="col-12 col-md-6 d-flex align-items-center">
                            <div class="form-check form-switch">
                                <input type="hidden" name="feature_on_bottom" value="0">
                                <input class="form-check-input" type="checkbox" name="feature_on_bottom" id="feature_on_bottom" value="1" {{ old('feature_on_bottom', $service_category->feature_on_bottom) == '1' ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" for="feature_on_bottom">Show at bottom section</label>
                            </div>
                        </div>


                        <!-- Type -->
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Type</label>
                                <select name="type" class="form-select rounded-3">
                                    <option value="Male" {{ old('type', $service_category->type) === 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('type', $service_category->type) === 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Both" {{ old('type', $service_category->type) === 'Both' ? 'selected' : '' }}>Both</option>
                                </select>
                            </div>
                        </div>

                        <!-- Parent Category -->
                        <div class="col-12 col-md-6">
                            <div class="mb-2">
                                <label class="form-label">Parent Category</label>
                                <select name="parent_id" class="form-select rounded-3">
                                    <option></option>
                                    @foreach ($service_categories as $category)
                                        @if ($category->id != $service_category->id)
                                            <option value="{{ $category->id }}" {{ old('parent_id', $service_category->parent_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->title }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Subcategories -->
                        <div class="col-12">
                            <div class="mb-2">
                                <label class="form-label">Sub Category</label>
                                <input type="text" name="categories-search" id="categories-search" class="form-control rounded-3" placeholder="Search Category By Name">
                                <div class="table-responsive mt-2">
                                    <table class="table table-striped table-bordered categories-table mb-0">
                                        <thead>
                                            <tr>
                                                <th style="width:48px"></th>
                                                <th>Name</th>
                                            </tr>
                                        </thead>
                                        <tbody id="categories-tbody">
                                            @php
                                                $selectedSubcategories = old('subcategoriesIds', $childCategoryIds ?? []);
                                            @endphp
                                            @foreach ($service_categories as $category)
                                                @if ($category->id != $service_category->id)
                                                    <tr>
                                                        <td>
                                                            <input class="category-checkbox" type="checkbox" name="subcategoriesIds[]" value="{{ $category->id }}" {{ in_array($category->id, $selectedSubcategories) ? 'checked' : '' }}>
                                                        </td>
                                                        <td>{{ $category->title }}</td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- SEO TAB -->
                <div class="tab-pane fade" id="seo" role="tabpanel" aria-labelledby="seo-tab">
                    <div class="row">
                        <!-- Slug -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="slug"><span style="color: red;">*</span><strong>SEO URL
                                        (Slug)</strong></label>
                                <input type="text" name="slug" id="slug" class="form-control"
                                    value="{{ old('slug', $service_category->slug ?? '') }}">
                                <small class="text-muted">
                                    • Should be lowercase with hyphens instead of spaces (e.g., "my-service") <br>
                                    • Avoid special characters and punctuation <br>
                                    • Should be unique across all services
                                </small>
                            </div>
                        </div>

                        <!-- Meta Title -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_title"><span style="color: red;">*</span><strong>Meta
                                        Title</strong></label>
                                <input type="text" name="meta_title" id="meta_title" class="form-control"
                                    value="{{ old('meta_title', $service_category->meta_title ?? '') }}" maxlength="60">
                                <small class="text-muted">• Recommended: 50-60 characters</small>
                            </div>
                        </div>

                        <!-- Meta Description -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_description"><strong>Meta Description</strong></label>
                                <textarea name="meta_description" id="meta_description" class="form-control" rows="4" maxlength="160">{{ old('meta_description', $service_category->meta_description ?? '') }}</textarea>
                                <small class="text-muted">• Recommended: 150-160 characters</small>
                            </div>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="meta_keywords"><strong>Meta Keywords</strong> (comma separated)</label>
                                <input type="text" name="meta_keywords" id="meta_keywords" class="form-control"
                                    value="{{ old('meta_keywords', $service_category->meta_keywords ?? '') }}"
                                    placeholder="keyword1, keyword2">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit -->
            <div class="d-grid gap-2 col-6 mx-auto mt-4">
                <button type="submit" class="btn btn-primary btn-lg">Update</button>
            </div>
        </form>
            </div>
        </div>
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
    <script>
        $(document).ready(function() {
            function sortCheckedToTop(tableId, checkboxClass) {
                const $table = $(tableId);
                const $rows = $table.find('tr');

                $rows.sort(function(a, b) {
                    const aChecked = $(a).find(checkboxClass).is(':checked');
                    const bChecked = $(b).find(checkboxClass).is(':checked');

                    if (aChecked && !bChecked) return -1;
                    if (!aChecked && bChecked) return 1;
                    return 0;
                });

                $table.append($rows);
            }

            sortCheckedToTop('#categories-tbody', '.category-checkbox');
        });
    </script>
@endsection
