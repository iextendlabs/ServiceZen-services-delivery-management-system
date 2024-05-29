@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <h2>Service Categories ({{ $total_service_category }})</h2>
                </div>
                <div class="float-right">
                    @can('service-category-create')
                        <a class="btn btn-success" href="{{ route('serviceCategories.create') }}"><i class="fa fa-plus"></i></a>
                    @endcan
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr>
        <div class="float-right">
            <form action="{{ route('serviceCategories.index') }}" method="GET">
                @csrf
                <div class="input-group flex-nowrap">
                    <input type="search" id="search_service_category" class="form-control" placeholder="Type to Search..."
                        aria-label="Search Product" name="title" value="{{ request('title') }}"
                        aria-describedby="addon-wrapping">
                </div>
                <div id="autocomplete-container" class="autocomplete-container"></div>
                <button type="submit" class="input-group-text float-right my-2 btn-primary"
                    id="addon-wrapping">Filter</button>
            </form>
        </div>
        <table class="table table-striped table-bordered">
            <tr>
                <th>Sr#</th>
                <th><a class=" ml-2"
                        href="{{ route('serviceCategories.index', array_merge(request()->query(), ['sort' => 'title', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Title</a>
                    @if (request('sort') === 'title')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th><a class=" ml-2"
                        href="{{ route('serviceCategories.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                    @if (request('sort') === 'description')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th><a class=" ml-2"
                        href="{{ route('serviceCategories.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                    @if (request('sort') === 'status')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th><a class=" ml-2"
                        href="{{ route('serviceCategories.index', array_merge(request()->query(), ['sort' => 'type', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Type</a>
                    @if (request('sort') === 'type')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th width="280px">Action</th>
            </tr>
            @if (count($service_categories))
                @foreach ($service_categories as $service_category)
                    <tr>
                        <td>{{ ++$i }}</td>
                            <td>
                                @if($service_category->parentCategoryForList) 
                                <a
                                    href="{{ route('services.index', ['category_id' => $service_category->parentCategoryForList->id]) }}">
                                    {{$service_category->parentCategoryForList->title}}
                                </a>
                                ->
                                @endif
                                <a href="{{ route('services.index', ['category_id' => $service_category->id]) }}">{{ $service_category->title }} </a>
                            </td>
                        <td>{{ $service_category->description }}</td>
                        <td>
                            @if ($service_category->status)
                                Enable
                            @else
                                Disable
                            @endif
                        </td>
                        <td>{{ $service_category->type }}</td>
                        <td>
                            <form id="deleteForm{{ $service_category->id }}"
                                action="{{ route('serviceCategories.destroy', $service_category->id) }}" method="POST">
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
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center">There is no service category.</td>
                </tr>
            @endif
        </table>
        {!! $service_categories->links() !!}
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
            var showingMore = false;

            $("#search_service_category").autocomplete({
                source: function(request, response) {
                    results = $.ui.autocomplete.filter(tags, request.term);
                    if (showingMore) {
                        response(results);
                    } else {
                        response(results.slice(0, 10));
                    }
                },
                open: function(event, ui) {
                    var $list = $(this).autocomplete("widget");
                    if (results.length > 10 && $list.find(".toggle-more").length === 0) {
                        $("<li>")
                            .append($("<a>").text(showingMore ? "Show Less" : "Show More")
                            .addClass("toggle-more").css({
                                "color": 'black',
                                "background-color": 'rgba(0, 0, 0, 0.10) !important',
                                "display": 'block',
                                "padding": "5px",
                                "text-decoration": "none",
                                "border-radius": "10px",
                                "text-align": "center",
                            }))
                            .appendTo($list);
                    }
                }
            }).autocomplete("instance")._renderItem = function(ul, item) {
                return $("<li>")
                    .append("<div>" + item.label + "</div>")
                    .appendTo(ul);
            };

            $(document).on("click", ".toggle-more", function(event) {
                event.preventDefault();
                showingMore = !showingMore;

                var $input = $("#search_service_category");
                $input.autocomplete("search", $input.val()); // Trigger search to refresh list
            });
            $("<style>")
            .prop("type", "text/css")
            .html("\
                .toggle-more:hover { \
                    background-color: rgba(0, 0, 0, 0.5) !important; \
                    border-radius: 10px; \
                    color: white !important; \
                } \
            ")
            .appendTo("head");
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
