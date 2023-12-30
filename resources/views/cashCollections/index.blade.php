@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Cash Collection</h2>
            </div>
            <div class="float-end">

                <a href="{{ request()->fullUrlWithQuery(['print' => '1']) }}" class="btn btn-danger float-end no-print"><i class="fa fa-print"></i> PDF</a>

                <a href="{{ request()->fullUrlWithQuery(['csv' => '1']) }}" class="btn btn-success float-end no-print" style="margin-right: 10px;"><i class="fa fa-download"></i> Excel</a>

                <a class="btn btn-danger float-end" href="{{ route('cashCollection.index') }}?status=Not Approved"" style=" margin-right: 10px;">
                    <i class="fas fa-times"></i> Not Approved
                </a>

                <a class="btn btn-success ml-2 float-end" href="{{ route('cashCollection.index') }}?status=Approved"" style=" margin-right: 10px;">
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
    <div class="row">
        <div class="col-md-12">
            <h3>Filter</h3>
            <hr>
            <form action="{{ route('cashCollection.index') }}" method="GET" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Order ID:</strong>
                            <div class="input-group mb-3">
                                <input type="number" name="order_id" class="form-control" value="{{ $filter_order_id }}">

                            </div>
                            <div class="float-right">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                            </div>

                        </div>
                    </div>
                </div>


        </div>
        </form>
    </div>
    <div class="col-md-12">
        <table class="table table-striped table-bordered">
            <tr>
                <th>SR#</th>
                <th>Order#</th>
                <th>Staff</th>
                <th>Collected Amount</th>
                <th>Customer</th>
                <th>Order Total</th>
                <th>Description</th>
                <th>Order Comment</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            @if(count($cash_collections))
            @foreach ($cash_collections as $cash_collection)
            <tr>
                <td>{{ ++$i }}</td>
                <td>{{ $cash_collection->order_id }}</td>
                <td>{{ $cash_collection->staff_name }}</td>
                <td>@currency($cash_collection->amount)</td>
                <td>{{ $cash_collection->order->customer->name }}</td>
                <td>@currency( $cash_collection->order->total_amount )</td>
                <td>{{ $cash_collection->description }}</td>
                <td> {{substr($cash_collection->order->order_comment, 0, 50)}}...</td>
                <td>{{ $cash_collection->status }}</td>
                <td>
                    <form id="deleteForm{{ $cash_collection->id }}" action="{{ route('cashCollection.destroy',$cash_collection->id) }}" method="POST">
                        @can('cash-collection-edit')
                        @if($cash_collection->status == 'Not Approved')
                        <a class="btn btn-sm btn-success" href="{{ route('cashCollectionUpdate',$cash_collection->id) }}?status=Approved">
                            <i class="fas fa-thumbs-up"></i>
                        </a>
                        @endif
                        <a class="btn btn-sm btn-danger" href="{{ route('cashCollectionUpdate',$cash_collection->id) }}?status=Not Approved">
                            <i class="fas fa-thumbs-down"></i>
                        </a>
                        @if(isset($cash_collection->image)) <br><br>
                        <a class="btn btn-sm btn-warning" href="/cash-collections-images/{{$cash_collection->image}}" target="_blank"><i class="fa fa-eye"></i> </a>
                        @endif

                        @endcan
                        @csrf
                        @method('DELETE')
                        @can('cash-collection-delete')
                        <button type="button" onclick="confirmDelete('{{ $cash_collection->id }}')" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
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