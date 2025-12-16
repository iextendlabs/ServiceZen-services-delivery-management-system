@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            @section('page_title')
            <h3 class="">Cash Collection</h3>
            @endsection
<div class="row align-items-center mt-3 no-print ml-1">
    <!-- Filter Section -->
    <div class="col-md-8 mb-3 mb-md-0">
        <form action="{{ route('cashCollection.index') }}" method="GET" enctype="multipart/form-data">
            <div class="form-row align-items-center">
                <div class="col-md-8 mb-2 mb-md-0">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"  style="border-radius: 0.75rem 0 0 0.75rem;">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="number" name="order_id" class="form-control border-left-0"
                            placeholder="Search by Order ID..." value="{{ $filter_order_id }}" style="border-left: 0; height: 2rem;">
                    </div>
                </div>
                <div class="col-md-4 d-flex">
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-sm btn-primary font-weight-bold mr-2">Apply Filter</button>
                            <a href="{{ url()->current() }}" class="btn btn-sm btn-light border font-weight-bold"><i class="fas fa-redo"></i> Reset</a>
                        </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Actions Dropdown -->
    <div class="col-md-4 text-md-right">
        <div class="dropdown">
            <button class="btn dropdown-toggle px-4" type="button" id="actionDropdown" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-ellipsis-h"></i> Actions
            </button>
            <div class="dropdown-menu dropdown-menu-right shadow-sm border-0" aria-labelledby="actionDropdown">
                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['print' => '1']) }}">
                    <i class="fa fa-file-pdf mr-2"></i> Export PDF
                </a>
                <a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['csv' => '1']) }}">
                    <i class="fa fa-file-excel mr-2"></i> Export Excel
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="{{ route('cashCollection.index') }}?status=Not Approved">
                    <i class="fas fa-times-circle mr-2"></i> Not Approved
                </a>
                <a class="dropdown-item " href="{{ route('cashCollection.index') }}?status=Approved">
                    <i class="fas fa-check-circle mr-2"></i> Approved
                </a>
            </div>
        </div>
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
        <div class="h3 text-secondary mb-4 section-heading font-weight-bold ml-3">
            <h3>Cash Collections ({{ $total_cash_collection }})</h3>
        </div>
        <div class="col-md-12">
            <table class="table table-hovere-bordered table-responsive">
                <tr>
                    <th>SR#</th>
                    <th class="">
                        <a class="ml-2  text-decoration-none"
                            href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Order</a>
                            @if (request('sort') === 'id')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                    </th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2  text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'staff_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Staff</a>
                            @if (request('sort') === 'staff_name')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2  text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Collected Amount</a>
                            @if (request('sort') === 'amount')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th>Customer</th>
                    <th>Order Total</th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2  text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                            @if (request('sort') === 'description')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th>Order Comment</th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2  text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                            @if (request('sort') === 'status')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th class="no-print">Action</th>
                </tr>
                @if (count($cash_collections))
                    @foreach ($cash_collections as $cash_collection)
                        <tr>
                            <td>{{ ++$i }}</td>
                            <td>{{ $cash_collection->order_id }}</td>
                            <td>{{ $cash_collection->staff_name }}</td>
                            <td>@currency($cash_collection->amount,true)</td>
                            <td>{{ $cash_collection->order->customer_name }}</td>
                            <td>@currency($cash_collection->order->total_amount,true)</td>
                            <td>{{ $cash_collection->description }}</td>
                            <td> {{ substr($cash_collection->order->order_comment, 0, 50) }}...</td>
                            <td>{{ $cash_collection->status }}</td>
                            <td class="no-print">
                                <form id="deleteForm{{ $cash_collection->id }}"
                                    action="{{ route('cashCollection.destroy', $cash_collection->id) }}" method="POST">
                                    @can('cash-collection-edit')
                                        @if ($cash_collection->status == 'Not Approved')
                                            <a class="btn btn-sm text-primary"
                                                href="{{ route('cashCollectionUpdate', $cash_collection->id) }}?status=Approved">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-sm"
                                            href="{{ route('cashCollectionUpdate', $cash_collection->id) }}?status=Not Approved">
                                            <i class="fas fa-thumbs-down"></i>
                                        </a>
                                        @if (isset($cash_collection->image))
                                            <br><br>
                                            <a class="btn btn-sm btn-warning"
                                                href="/cash-collections-images/{{ $cash_collection->image }}"
                                                target="_blank"><i class="fa fa-eye"></i> </a>
                                        @endif
                                    @endcan
                                    @csrf
                                    @method('DELETE')
                                    @can('cash-collection-delete')
                                        <button type="button" onclick="confirmDelete('{{ $cash_collection->id }}')"
                                            class="btn btn-sm"><i class="fas fa-trash text-danger"></i></button>
                                    @endcan
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8" class="text-center">There is no Cash Collection.</td>
                    </tr>
                @endif
            </table>
            {!! $cash_collections->links() !!}
        </div>
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
@endsection
