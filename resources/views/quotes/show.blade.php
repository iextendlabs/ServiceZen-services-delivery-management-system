@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <div class="float-start">
                <h2>Show Quote</h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <strong>User Name:</strong>
                {{ $quote->user->name ?? "" }}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Service:</strong>
                {{ $quote->service_name }}
            </div>
        </div>
        @if($quote->serviceOption)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Option:</strong>
                {{ $quote->serviceOption->option_name }}(@currency($quote->serviceOption->option_price, true))
            </div>
        </div>
        @endif
        <div class="col-md-12">
            <div class="form-group">
                <strong>Detail:</strong>
                {!! $quote->detail !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <strong>Status:</strong>
                {!! $quote->status !!}
            </div>
        </div>
        @if(count($quote->categories) > 0)
        <div class="col-md-12">
            <div class="form-group">
                <strong>Categories:</strong>
                <ul>
                    @foreach($quote->categories as $category)
                        <li>{{ $category->title }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
    </div>
    @if(auth()->user()->hasRole("Admin") && count($quote->staffs) > 0)
    <div class="row">
        <hr>
        <h3>Assigned Staff</h3>
        <table class="table table-striped table-bordered album bg-light">
            <tr>
                <th>Name</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            @foreach ($quote->staffs as $staff)
                <tr>
                    <td>{{ $staff->name ?? "" }}</td>
                    <td>{{ $staff->pivot->status ?? "" }}</td>
                    <td>
                        <form action="{{ route('quotes.detachStaff', ['quote' => $quote->id, 'staff' => $staff->id]) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to remove this staff?')">
                                <i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </table>
    </div>
    @endif
</div>
@endsection