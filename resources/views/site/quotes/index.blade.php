@extends('site.layout.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center">
                <h2>Quotes</h2>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-borderless">
                        <thead class="border-bottom">
                            <tr>
                                <th>Service</th>
                                <th>Send by</th>
                                <th class="text-center">Status</th>
                                <th>Date Added</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($quotes))
                                @foreach ($quotes as $quote)
                                    <tr class="border-bottom">
                                        <td>
                                            <div class="media">
                                                @if ($quote->service->image)
                                                    <img src="{{ asset('service-images/' . $quote->service->image) }}"
                                                        alt="Service Image" class="mr-3 rounded" width="auto"
                                                        height="80">
                                                @endif
                                                <div class="media-body">
                                                    <h6 class="mt-1 mb-1">{{ $quote->service_name }}</h6>
                                                    @if ($quote->sourcing_quantity)
                                                        <span class="text-muted">{{ $quote->sourcing_quantity }}
                                                            Quantity</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong>{{ $quote->user->name ?? 'Unknown' }}</strong>
                                        </td>
                                        <td class="text-center">
                                            <div>
                                                <strong>{{ $quote->status }}</strong>
                                            </div>

                                            @if ($quote->bid)
                                                <div class="mt-2">
                                                    <a href="{{ route('site.quote.bid', ['quote_id' => $quote->id, 'staff_id' => $quote->bid->staff_id]) }}"
                                                        class="btn btn-primary btn-sm">
                                                        Selected Bid
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $quote->created_at }}</td>

                                        <td>
                                            <a class="btn btn-outline-primary"
                                                href="{{ route('siteQuotes.show', $quote->id) }}">
                                                View Detail
                                            </a>
                                            <a href="{{ route('site.quote.bids', ['quote_id' => $quote->id]) }}"
                                                class="btn btn-primary">
                                                <i class="fas fa-eye"></i> View Bids
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center">There are no Quote.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                {!! $quotes->links() !!}
            </div>
        </div>
    </div>
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
