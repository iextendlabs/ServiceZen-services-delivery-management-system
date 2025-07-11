@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row mb-3">
            <div class="col-md-9">
                <div class="float-left">
                    <h2>Service Categories ({{ $total_service_category }})</h2>
                </div>
            </div>
            <div class="col-md-3 text-end">
                @can('service-category-create')
                    <a class="btn btn-success float-end" href="{{ route('serviceCategories.create') }}"><i
                            class="fa fa-plus"></i></a>
                @endcan
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <!-- Listing Table -->
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Type</th>
                        <th>Feature</th>
                        <th>Bottom</th>
                        <th width="280px">Action</th>
                    </tr>
                    @forelse ($service_categories as $service_category)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>
                                @if (!auth()->user()->hasRole('Data Entry') && $service_category->parentCategoryForList)
                                    <a
                                        href="{{ route('services.index', ['category_id' => $service_category->parentCategoryForList->id]) }}">
                                        {{ $service_category->parentCategoryForList->title }}
                                    </a> ->
                                @endif
                                <a
                                    href="{{ route('services.index', ['category_id' => $service_category->id]) }}">{{ $service_category->title }}</a>
                            </td>
                            <td>{{ $service_category->description }}</td>
                            <td>{{ $service_category->status ? 'Enable' : 'Disable' }}</td>
                            <td>{{ $service_category->type }}</td>
                            <td>{{ $service_category->feature ? 'Yes' : 'No' }}</td>
                            <td>{{ $service_category->feature_on_bottom ? 'Yes' : 'No' }}</td>
                            <td>
                                <form id="deleteForm{{ $service_category->id }}"
                                    action="{{ route('serviceCategories.destroy', $service_category->id) }}"
                                    method="POST">
                                    @can('FAQs-create')
                                        <a class="btn btn-primary"
                                            href="{{ route('FAQs.create', ['category_id' => $service_category->id]) }}">Add
                                            FAQs</a>
                                    @endcan
                                    <a class="btn btn-warning"
                                        href="{{ route('serviceCategories.show', $service_category->id) }}"><i
                                            class="fa fa-eye"></i></a>
                                    @can('service-category-edit')
                                        <a class="btn btn-primary"
                                            href="{{ route('serviceCategories.edit', $service_category->id) }}"><i
                                                class="fa fa-edit"></i></a>
                                    @endcan
                                    @csrf
                                    @method('DELETE')
                                    @can('service-category-delete')
                                        <button type="button" onclick="confirmDelete('{{ $service_category->id }}')"
                                            class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                    @endcan
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center">No service category found.</td>
                        </tr>
                    @endforelse
                </table>
                {!! $service_categories->links() !!}
            </div>

            <!-- Filters Sidebar -->
            <div class="col-md-3">
                <form action="{{ route('serviceCategories.index') }}" method="GET" class="card p-3">
                    <h5>Filters</h5>
                    <div class="form-group mb-3">
                        <label for="search_service_category">Title</label>
                        <input type="text" name="title" id="search_service_category" class="form-control"
                            placeholder="Search by Title" value="{{ $filter['title'] ?? '' }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="feature">Feature</label>
                        <select name="feature" id="feature" class="form-control">
                            <option value="">-- All --</option>
                            <option value="1"
                                {{ isset($filter['feature']) && $filter['feature'] === '1' ? 'selected' : '' }}>Yes
                            </option>
                            <option value="0"
                                {{ isset($filter['feature']) && $filter['feature'] === '0' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label for="feature_on_bottom">Feature on Bottom</label>
                        <select name="feature_on_bottom" id="feature_on_bottom" class="form-control">
                            <option value="">-- All --</option>
                            <option value="1"
                                {{ isset($filter['feature_on_bottom']) && $filter['feature_on_bottom'] === '1' ? 'selected' : '' }}>
                                Yes</option>
                            <option value="0"
                                {{ isset($filter['feature_on_bottom']) && $filter['feature_on_bottom'] === '0' ? 'selected' : '' }}>
                                No</option>
                        </select>
                    </div>

                    <div class="form-group d-flex justify-content-between">
                        <button type="submit" class="btn btn-primary">Apply</button>
                        <a href="{{ route('serviceCategories.index') }}" class="btn btn-secondary">Reset</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        jQuery(document).ready(function($) {
            var availableTags = [];
            var results = [];

            $.ajax({
                method: "GET",
                url: "/service-category-list",
                success: function(response) {
                    availableTags = response;
                    startAutocomplete(availableTags);
                }
            });

            function startAutocomplete(tags) {
                $("#search_service_category").autocomplete({
                    source: function(request, response) {
                        results = $.ui.autocomplete.filter(tags, request.term);
                        response(results.slice(0, 10));

                    },
                    open: function(event, ui) {
                        var $list = $(this).autocomplete("widget");
                        if (results && results.length >= 10) {
                            $("<li>")
                                .append($("<a>").text("Show More").addClass("show-more").css("color",
                                    "blue"))
                                .appendTo($list);
                        }
                    }
                }).autocomplete("instance")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<div>" + item.label + "</div>")
                        .appendTo(ul);
                };
                $(document).on("click", ".show-more", function(event) {
                    event.preventDefault();
                    var $input = $("#search_service_category");
                    var $list = $input.autocomplete("widget");
                    var buttonText = $(this).text();

                    if (buttonText === "Show More") {
                        $input.autocomplete("option", "source", tags);
                        $input.autocomplete("search", $input.val()); // Trigger search to refresh list
                        $(this).text("Show Less");
                    } else {
                        // Show only the first 10 items
                        $input.autocomplete("option", "source", function(request, response) {
                            var results = $.ui.autocomplete.filter(tags, request.term);
                            response(results.slice(0, 10));
                        });
                        $(this).text("Show More"); // Change button text to "Show More"
                    }
                });
            }
        });
    </script>

    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });
        });
    </script>
@endsection
