@extends('layouts.app')
@section('content')
    <style>
        /* Positioning Buttons */
        .fixed-left {
            position: absolute;
            top: 50%;
            left: 10px;
            transform: translateY(-50%);
            font-size: 24px;
            padding: 10px 15px;
            z-index: 1050;
        }

        .fixed-right {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            font-size: 24px;
            padding: 10px 15px;
            z-index: 1050;
        }

        /* Close Button */
        .close-button {
            position: absolute;
            top: 10px;
            right: 10px;
            font-size: 20px;
            padding: 5px 10px;
            z-index: 1050;
        }
    </style>
    @php
        $user = auth()->user();
        $staffQuote = $quote->staffs->where('id', $user->id)->first();
    @endphp
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4 shadow-sm border-0">
                    <div class="card-header bg-light font-weight-bold">
                        Quote Details
                        <div class="float-end">

                            @if ($user->hasRole('Staff'))
                                @if ($staffQuote && $staffQuote->pivot->status == 'Pending')
                                    <button type="button" class="btn btn-success accept-quote" data-id="{{ $quote->id }}"
                                        data-amount="{{ $staffQuote->pivot->quote_amount }}"
                                        data-commission="{{ $staffQuote->pivot->quote_commission }}">Accept</button>
                                    <button type="button" class="btn btn-danger reject-quote"
                                        data-id="{{ $quote->id }}">Reject</button>
                                @endif
                                @if (is_null($quote->bid_id) || ($quote->bid && $quote->bid->staff_id == auth()->id()))
                                    @if ($staffQuote->pivot->status == 'Accepted')
                                        <a href="{{ route('quote.bid', ['quote_id' => $quote->id, 'staff_id' => auth()->id()]) }}"
                                            class="btn btn-primary">
                                            Bid
                                        </a>
                                    @endif
                                @endif
                            @endif
                            @if ($user->hasRole('Admin'))
                                <a href="{{ route('quote.bids', ['quote_id' => $quote->id]) }}" class="btn btn-primary">
                                    <i class="fas fa-eye"></i> View Bids
                                </a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- User Info -->
                        <h6><strong>Send by {{ $quote->user->name ?? 'N/A' }}</strong></h6>
                        @if (
                            $user->hasRole('Admin') ||
                                ($user->hasROle('Staff') && $user->staff->show_quote_detail == '1' && $staffQuote->pivot->status == 'Accepted'))
                            <p><i class="fas fa-phone fa-sm"></i> {{ $quote->phone }}</p>
                            <p><i class="fab fa-whatsapp text-success"></i> {{ $quote->whatsapp }}</p>
                            @if ($quote->location)
                                <p>
                                    <i class="fas fa-map-marker-alt text-danger"></i>
                                    <a target="_blank" href="https://maps.google.com/?q={{ urlencode($quote->location) }}">
                                        {{ $quote->location }}
                                    </a>
                                </p>
                            @endif
                        @endif
                        <hr>

                        <!-- Service Info -->
                        <h6><strong>Service Detail:</strong></h6>

                        <div class="d-flex align-items-center">
                            @if ($quote->service->image)
                                <img src="{{ asset('service-images/' . $quote->service->image) }}" alt="Service Image"
                                    class="rounded" width="auto" height="80">
                            @endif
                            <div class="ml-3">
                                <h6 class="mt-0 mb-1">{{ $quote->service_name }}</h6>
                                @if ($quote->sourcing_quantity)
                                    <span class="text-muted">{{ $quote->sourcing_quantity }}
                                        Quantity</span>
                                @endif
                            </div>
                        </div>
                        @if ($quote->serviceOption)
                            <ul>
                                @foreach ($quote->serviceOption as $option)
                                    <li>
                                        <p class="text-muted">
                                            {{ $option->option_name }} - @currency($option->option_price, true)
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        <hr>

                        <!-- Status & Message -->
                        <p><strong>Status:</strong> <span class="badge badge-info">{{ $quote->status }}</span></p>
                        @if ($user->hasRole('Staff'))
                            @if ($staffQuote)
                                <p><strong>Quote Amount:</strong> AED{{ $staffQuote->pivot->quote_amount ?? 0 }}</p>
                                <p><strong>Quote Commission:</strong> {{ $staffQuote->pivot->quote_commission ?? 0 }}%</p>
                            @endif
                        @endif
                        <p><strong>Message:</strong> {{ $quote->detail }}</p>

                        @if ($quote->images)
                            <div class="row">
                                <div class="col-md-12">
                                    @foreach ($quote->images as $key => $image)
                                        <img src="{{ asset('quote-images/' . $image->image) }}" alt="Inquiry Image"
                                            class="img-thumbnail gallery-image" data-toggle="modal"
                                            data-target="#imageModal" data-index="{{ $key }}"
                                            data-image="{{ asset('quote-images/' . $image->image) }}"
                                            style="width: 150px; height: 150px; object-fit: cover;">
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Full Screen Image Modal -->
                        <div class="modal fade" id="imageModal" tabindex="-1" role="dialog"
                            aria-labelledby="imageModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                                <div class="modal-content position-relative">
                                    <button id="prevImage" class="btn btn-dark position-absolute fixed-left">❮</button>
                                    <button id="nextImage" class="btn btn-dark position-absolute fixed-right">❯</button>

                                    <div class="modal-body text-center">
                                        <img id="modalImage" src="" class="img-fluid rounded shadow-lg">
                                    </div>
                                    <button type="button" class="close-button" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">×</span>
                                    </button>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($user->hasRole('Admin') && count($quote->staffs) > 0)
            <div class="row">
                <hr>
                <h3>Assigned Staff</h3>
                <table class="table table-striped table-bordered album bg-light">
                    <tr>
                        <th>Name</th>
                        <th>Status</th>
                        <th>Quote Amount</th>
                        <th>Quote Commission</th>
                        <th>Action</th>
                    </tr>
                    @foreach ($quote->staffs as $staff)
                        <tr>
                            <td>{{ $staff->name ?? '' }}</td>
                            <td>{{ $staff->pivot->status ?? '' }}</td>
                            <td>
                                <input type="number" step="0.01" class="form-control quote-amount" data-quote-id="{{ $quote->id }}"
                                    data-staff-id="{{ $staff->id }}" value="{{ $staff->pivot->quote_amount ?? '' }}">
                                    <small class="form-text text-muted">Minimum value: 0.01</small>
                            </td>
                            <td>
                                <input type="number" class="form-control quote-commission"
                                    data-quote-id="{{ $quote->id }}" data-staff-id="{{ $staff->id }}"
                                    value="{{ $staff->pivot->quote_commission ?? '' }}">
                            </td>
                            <td>
                                <form
                                    action="{{ route('quotes.detachStaff', ['quote' => $quote->id, 'staff' => $staff->id]) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                        onclick="return confirm('Are you sure you want to remove this staff?')">
                                        <i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </table>
            </div>
        @endif

    </div>
    <script>
        $(document).ready(function() {
            let images = [];
            let currentIndex = 0;

            // Store images in an array
            $(".gallery-image").each(function() {
                images.push($(this).data("image"));
            });

            $(".gallery-image").click(function() {
                currentIndex = parseInt($(this).data("index"));
                updateModalImage();
            });

            $("#prevImage").click(function() {
                if (currentIndex > 0) {
                    currentIndex--;
                    updateModalImage();
                }
            });

            $("#nextImage").click(function() {
                if (currentIndex < images.length - 1) {
                    currentIndex++;
                    updateModalImage();
                }
            });

            function updateModalImage() {
                $("#modalImage").attr("src", images[currentIndex]);

                // Disable buttons if at start or end
                $("#prevImage").prop("disabled", currentIndex === 0);
                $("#nextImage").prop("disabled", currentIndex === images.length - 1);
            }

            $('.show-map-btn').on('click', function() {
                var coordinates = $(this).data('coordinates');
                var [latitude, longitude] = coordinates.split(',').map(coord => parseFloat(coord.trim()));

                // Open a new window or redirect to a map URL with the coordinates
                var mapUrl = `https://maps.google.com/?q=${latitude},${longitude}`;
                window.open(mapUrl, '_blank');
            });

            function updateQuoteData(staffId, quoteId, field, value) {
                $.ajax({
                    url: "{{ route('quotes.updateStaffData') }}",
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        staff_id: staffId,
                        quote_id: quoteId,
                        field: field,
                        value: value
                    },
                    success: function(response) {
                        console.log("Updated successfully");
                    },
                    error: function(error) {
                        console.error("Error updating data", error);
                    }
                });
            }

            $(".quote-amount, .quote-commission").on("change", function() {
                let staffId = $(this).data("staff-id");
                let quoteId = $(this).data("quote-id");
                let field = $(this).hasClass("quote-amount") ? "quote_amount" : "quote_commission";
                let value = $(this).val();

                updateQuoteData(staffId, quoteId, field, value);
            });

            $('.accept-quote').click(function() {
                let quoteId = $(this).data('id');
                let amount = $(this).data('amount');
                let commission = $(this).data('commission');

                if (confirm(
                        `Are you sure you want to accept this quote? Upon acceptance, your balance will be adjusted by ${amount} AED. If you win the bid, ${commission}% of your bid value will be deducted from your balance.`
                        )) {
                    updateQuoteStatus(quoteId, 'Accepted');
                }
            });

            $('.reject-quote').click(function() {
                let quoteId = $(this).data('id');
                if (confirm('Are you sure you want to reject this quote?')) {
                    updateQuoteStatus(quoteId, 'Rejected');
                }
            });

            function updateQuoteStatus(quoteId, status) {
                $.ajax({
                    url: '{{ route('quotes.updateStatus') }}',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    data: {
                        id: quoteId,
                        status: status
                    },
                    success: function(response) {
                        alert(response.message);
                        location.reload();
                    },
                    error: function(error) {
                        console.error("Error:", error);
                        alert(error.responseJSON.error);
                    }
                });
            }
        });
    </script>
@endsection
