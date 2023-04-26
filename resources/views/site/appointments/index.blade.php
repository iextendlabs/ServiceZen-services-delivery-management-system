@extends('site.layout.app')
<base href="/public">
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Your Booked Service</h2>
    </div>
</div>
<div class="container">
    <div>
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
    @if(count($booked_services) != 0)
    <table class="table table-bordered album bg-light">
        <tr>
            <th>No</th>
            <th>Service</th>
            <th>Price</th>
            <th>Status</th>
            <th>date</th>
            <th>Time</th>
            <th>Action</th>
        </tr>
        @foreach ($booked_services as $booked_service)
        <tr>
            <td>{{ ++$i }}</td>
            <td>{{ $booked_service->service->name }}</td>
            <td>{{ $booked_service->service->price }}</td>
            <td>{{ $booked_service->status }}</td>
            <td>{{ $booked_service->date }}</td>
            <td>{{ $booked_service->time }}</td>
            <td>
                <form action="{{ route('booking.destroy',$booked_service->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </td>
        </tr>
        @endforeach
        
    </table>
    @else
    <div class="text-center">
        <h4>Cart is Empty</h4>
    </div>
    @endif
    @if(count($booked_services))
        <div class="text-center">
        <a href="CartCheckout">
            <button type="button" class="btn btn-success">Checkout</button>
        </a>
        </div>
    @endif
    
  </div>
</div>
@endsection