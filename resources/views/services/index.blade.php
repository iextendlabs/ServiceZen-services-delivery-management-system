@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Services</h2>
                </div>
                <div class="float-end d-flex align-items-center">
                    @can('service-edit')
                        <div class="input-group me-2">
                            <select name="bulk-status" class="form-control">
                                <option value="1">Enable</option>
                                <option value="0">Disable</option>
                            </select>
                            <div class="input-group-append">
                                <button id="bulkEditBtn" class="btn btn-primary" type="button"><i
                                        class="fa fa-save"></i></button>
                            </div>
                        </div>
                    @endcan

                    @can('service-create')
                        <a class="btn btn-primary me-2" href="{{ route('services.create') }}"><i class="fa fa-plus"></i></a>
                    @endcan

                    <button id="bulkCopyBtn" class="btn btn-secondary me-2" type="button"><i
                            class="fa fa-copy"></i></button>

                    @can('service-delete')
                        <button id="bulkDeleteBtn" class="btn btn-danger" type="button"><i class="fa fa-trash"></i></button>
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
        <div class="row">
            <div class="col-md-12">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('services.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter['name'] }}" class="form-control"
                                    placeholder="Name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Price:</strong>
                                <input type="number" name="price" value="{{ $filter['price'] }}" class="form-control"
                                    placeholder="Price">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Category:</strong>
                                <select name="category_id" class="form-control">
                                    <option></option>
                                    @foreach ($service_categories as $category)
                                        @if ($category->id == $filter['category_id'])
                                            <option value="{{ $category->id }}" selected>{{ $category->title }}</option>
                                        @else
                                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="d-flex flex-wrap justify-content-md-end">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                                </div>
                                <div class="col-md-9 mb-3">
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <h3>Services ({{ $total_service }})</h3>
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th></th>
                        <th class="text-left"><a
                                href="{{ route('services.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th class="text-right"><a
                                href="{{ route('services.index', array_merge(request()->query(), ['sort' => 'price', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Price</a>
                            @if (request('sort') === 'price')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th class="text-right"><a
                            href="{{ route('services.index', array_merge(request()->query(), ['sort' => 'duration', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Duration</a>
                            @if (request('sort') === 'duration')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif    
                        </th>
                        <th class="text-right"><a
                            href="{{ route('services.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                            @if (request('sort') === 'status')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif     
                        </th>
                        <th class="text-left"><a
                            href="{{ route('services.index', array_merge(request()->query(), ['sort' => 'type', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Type</a>
                            @if (request('sort') === 'type')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif    
                        </th>
                        <th class="text-right">Action</th>
                    </tr>
                    @if (count($services))
                        @foreach ($services as $service)
                            <tr>
                                <td>
                                    <input type="checkbox" class="item-checkbox" value="{{ $service->id }}">
                                </td>
                                <td class="text-left">{{ $service->name }}</td>
                                <td class="text-right">@currency($service->price)</td>
                                <td class="text-right">{{ $service->duration }}</td>
                                <td class="text-right">
                                    @if ($service->status)
                                        Enable
                                    @else
                                        Disable
                                    @endif
                                </td>
                                <td class="text-left">{{ $service->type }}</td>
                                <td class="text-right">
                                    <form id="deleteForm{{ $service->id }}"
                                        action="{{ route('services.destroy', $service->id) }}" method="POST">
                                        <a class="btn btn-warning" href="/serviceDetail/{{ $service->id }}">Store View</a>
                                        @can('FAQs-create')
                                            <a class="btn btn-primary"
                                                href="{{ route('FAQs.create', ['service_id' => $service->id]) }}">Add FAQs</a>
                                        @endcan
                                        <a class="btn btn-warning" href="{{ route('services.show', $service->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('service-edit')
                                            <a class="btn btn-primary" href="{{ route('services.edit', $service->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('service-delete')
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete('{{ $service->id }}')">Delete</button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4" class="text-center">There is no service.</td>
                        </tr>
                    @endif
                </table>
                {!! $services->links() !!}
            </div>

        </div>
    </div>
    <script>
        $('#bulkDeleteBtn').click(function() {
            const selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedItems.length > 0) {
                if (confirm('Are you sure you want to delete the selected items?')) {
                    deleteSelectedItems(selectedItems);
                }
            } else {
                alert('Please select items to delete.');
            }
        });

        $('#bulkCopyBtn').click(function() {
            const selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();

            if (selectedItems.length > 0) {
                if (confirm('Are you sure you want to Copy the selected items?')) {
                    copySelectedItems(selectedItems);
                }
            } else {
                alert('Please select items to Copy.');
            }
        });

        $('#bulkEditBtn').click(function() {
            const selectedItems = $('.item-checkbox:checked').map(function() {
                return $(this).val();
            }).get();
            const status_value = $('select[name="bulk-status"]').val();
            const status_text = $('select[name="bulk-status"] option:selected').text();

            if (selectedItems.length > 0) {
                if (confirm("Are you sure you want to Set " + status_text + " to selected items?")) {
                    editSelectedItems(selectedItems, status_value);
                }
            } else {
                alert('Please select items to Set Status.');
            }
        });


        function deleteSelectedItems(selectedItems) {
            $.ajax({
                url: '{{ route('services.bulkDelete') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function copySelectedItems(selectedItems) {
            $.ajax({
                url: '{{ route('services.bulkCopy') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function editSelectedItems(selectedItems, status) {
            $.ajax({
                url: '{{ route('services.bulkEdit') }}',
                method: 'POST',
                dataType: 'json',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: JSON.stringify({
                    selectedItems,
                    status
                }),
                success: function(data) {
                    alert(data.message);
                    window.location.reload();
                },
                error: function(error) {
                    console.error('Error:', error);
                }
            });
        }

        function confirmDelete(serviceId) {
            var result = confirm("Are you sure you want to delete this service?");
            if (result) {
                document.getElementById('deleteForm' + serviceId).submit();
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
