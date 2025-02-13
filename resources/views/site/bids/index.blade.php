@extends('site.layout.app')

@section('content')
<div class="container">
    <div class="bids-container">
        <h2 class="mb-4 text-center">All Bids for Quote #{{ $quote->id }}</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card">
            <div class="card-header bg-primary text-white text-center">Staff Bids</div>
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Staff Name</th>
                            <th>Bid Amount</th>
                            <th>Comment</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="bids-table">
                        @foreach($bids as $bid)
                            <tr>
                                <td>{{ $bid->staff->name }}</td>
                                <td>AED{{ number_format($bid->bid_amount, 2) }}</td>
                                <td>{{ $bid->comment ?? 'No comment' }}</td>
                                <td>
                                    <a href="{{ route('site.quote.bid', ['quote_id' => $quote->id, 'staff_id' => $bid->staff->id]) }}" 
                                       class="btn btn-success btn-sm">
                                        <i class="fas fa-comments"></i> Chat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
@endsection
