@extends('layouts.app')
@section('content')
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
                    <form action="{{ route('cashCollection.destroy',$cash_collection->id) }}" method="POST">
                        @can('cash-collection-edit')
                        @if($cash_collection->status == 'Not Approved')
                        <a class="btn btn-sm btn-success" href="{{ route('cashCollectionUpdate',$cash_collection->id) }}?status=Approved">
                            <i class="fas fa-thumbs-up"></i>
                        </a>
                        @endif
                        <a class="btn btn-sm btn-danger" href="{{ route('cashCollectionUpdate',$cash_collection->id) }}?status=Not Approved">
                            <i class="fas fa-thumbs-down"></i>
                        </a>

                        @endcan
                        @csrf
                        @method('DELETE')
                        @can('cash-collection-delete')
                        <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
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

@endsection