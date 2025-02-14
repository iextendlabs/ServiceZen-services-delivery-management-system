@extends('site.layout.app')

@section('content')
    <div class="container">
        <div class="bids-container">
            <h2 class="mb-4 text-center">All Bids for Quote #{{ $quote->id }}</h2>

            @if (session('success'))
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
                            @if (count($bids))
                                @foreach ($bids as $bid)
                                    <tr class="{{ $quote->bid_id == $bid->id ? 'table-success' : '' }}">
                                        <td>{{ $bid->staff->name }}</td>
                                        <td>AED{{ number_format($bid->bid_amount, 2) }}</td>
                                        <td>{{ $bid->comment ?? 'No comment' }}</td>
                                        <td>
                                            @if ($quote->bid_id == $bid->id)
                                                <span class="badge badge-success">Selected Bid</span>
                                            @endif
                                            @if (is_null($quote->bid_id))
                                                <button class="btn btn-success btn-sm confirm-bid"
                                                    data-quote-id="{{ $quote->id }}" data-bid-id="{{ $bid->id }}">
                                                    Confirm
                                                </button>
                                            @endif
                                            @if (is_null($quote->bid_id) || $quote->bid_id == $bid->id)
                                                <a href="{{ route('site.quote.bid', ['quote_id' => $quote->id, 'staff_id' => $bid->staff->id]) }}"
                                                    class="btn btn-primary btn-sm">
                                                    <i class="fas fa-comments"></i> Chat
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">There are no Bid.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.confirm-bid').click(function() {
                var quoteId = $(this).data('quote-id');
                var bidId = $(this).data('bid-id');

                $.ajax({
                    url: "{{ route('siteQuote.updateStatus') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        id: quoteId,
                        bid_id: bidId
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload(); // Reload to reflect changes
                    },
                    error: function(xhr) {
                        alert("Error: " + xhr.responseJSON.message);
                    }
                });
            });
        });
    </script>
@endsection
