@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 text-center mb-3">
                <h2>Cash Collection</h2>
            </div>
            <div class="col-md-12 mb-3 no-print">
                <div class="d-flex flex-wrap justify-content-md-end">
                    <a href="{{ request()->fullUrlWithQuery(['print' => '1']) }}" class="btn btn-danger mb-2"><i
                            class="fa fa-print"></i> PDF</a>

                    <a href="{{ request()->fullUrlWithQuery(['csv' => '1']) }}" class="btn btn-success mb-2 ms-md-2"><i
                            class="fa fa-download"></i> Excel</a>

                    <a class="btn btn-danger mb-2 ms-md-2" href="{{ route('cashCollection.index') }}?status=Not Approved">
                        <i class="fas fa-times"></i> Not Approved
                    </a>

                    <a class="btn btn-success mb-2 ms-md-2" href="{{ route('cashCollection.index') }}?status=Approved">
                        <i class="fas fa-check"></i> Approved
                    </a>
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
        <div class="row no-print">
            <div class="col-md-12">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('cashCollection.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Order ID:</strong>
                                <div class="input-group mb-3">
                                    <input type="number" name="order_id" class="form-control"
                                        value="{{ $filter_order_id }}">

                                </div>
                                <div class="float-right">
                                    <button type="submit" class="btn btn-primary">Filter</button>
                                    <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <h3>Cash Collections ({{ $total_cash_collection }})</h3>
        <div class="col-md-12">
            <table class="table table-striped table-bordered table-responsive">
                <tr>
                    <th>SR#</th>
                    <th class="">
                        <a class="ml-2 text-black text-decoration-none"
                            href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Order</a>
                            @if (request('sort') === 'id')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                    </th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2 text-black text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'staff_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Staff</a>
                            @if (request('sort') === 'staff_name')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2 text-black text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'amount', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Collected Amount</a>
                            @if (request('sort') === 'amount')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th>Customer</th>
                    <th>Order Total</th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2 text-black text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'description', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Description</a>
                            @if (request('sort') === 'description')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif

                        </div>
                    </th>
                    <th>Order Comment</th>
                    <th>
                        <div class="d-flex">
                            <a class="ml-2 text-black text-decoration-none" href="{{ route('cashCollection.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
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
                            <td>@currency($cash_collection->amount)</td>
                            <td>{{ $cash_collection->order->customer->name }}</td>
                            <td>@currency($cash_collection->order->total_amount)</td>
                            <td>{{ $cash_collection->description }}</td>
                            <td> {{ substr($cash_collection->order->order_comment, 0, 50) }}...</td>
                            <td>{{ $cash_collection->status }}</td>
                            <td class="no-print">
                                <form id="deleteForm{{ $cash_collection->id }}"
                                    action="{{ route('cashCollection.destroy', $cash_collection->id) }}" method="POST">
                                    @can('cash-collection-edit')
                                        @if ($cash_collection->status == 'Not Approved')
                                            <a class="btn btn-sm btn-success"
                                                href="{{ route('cashCollectionUpdate', $cash_collection->id) }}?status=Approved">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                        @endif
                                        <a class="btn btn-sm btn-danger"
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
                                            class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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
