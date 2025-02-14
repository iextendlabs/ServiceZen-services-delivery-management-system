@extends('site.layout.app')

@section('content')
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-12">
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-light font-weight-bold">
                        Quote Details
                    </div>
                    <div class="card-body">
                        <!-- User Info -->
                        <h6><strong>Sent by {{ $quote->user->name ?? 'N/A' }}</strong> </h6>
                        @if ($quote->phone)
                            <p class="mb-1"><i class="fas fa-phone text-success"></i> {{ $quote->phone }}</p>
                        @endif
                        @if ($quote->whatsapp)
                            <p><i class="fab fa-whatsapp text-success"></i> {{ $quote->whatsapp }}</p>
                        @endif

                        <hr>

                        <!-- Service Info -->
                        <h6><strong>Service:</strong> {{ $quote->service_name }}</h6>
                        @if ($quote->serviceOption)
                            <p class="text-muted">
                                {{ $quote->serviceOption->option_name }} - @currency($quote->serviceOption->option_price, true)
                            </p>
                        @endif
                        @if ($quote->sourcing_quantity)
                            <p class="text-muted">Quantity: {{ $quote->sourcing_quantity }}</p>
                        @endif

                        @if ($quote->service->image)
                            <img src="{{ asset('service-images/' . $quote->service->image) }}" alt="Service Image"
                                class="rounded shadow-sm img-fluid mb-3" style="max-width: 120px;">
                        @endif

                        <hr>

                        <!-- Status & Message -->
                        <p><strong>Status:</strong> <span class="badge badge-info">{{ $quote->status }}</span></p>
                        <p><strong>Message:</strong> {{ $quote->detail }}</p>

                        @if ($quote->image)
                            <img src="{{ asset('quote-images/' . $quote->image) }}" alt="Inquiry Image"
                                class="rounded shadow-sm img-fluid" style="max-width: 100%;">
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
