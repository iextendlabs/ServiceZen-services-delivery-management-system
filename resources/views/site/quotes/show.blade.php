@extends('site.layout.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-primary shadow-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h3 class="mb-0">Quote Details</h3>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h5 class="text-primary">User Name</h5>
                        <p class="font-weight-bold">{{ $quote->user->name ?? 'N/A' }}</p>
                    </div>
                    <div class="mb-3">
                        <h5 class="text-primary">Service</h5>
                        <p class="font-weight-bold">{{ $quote->service_name }}</p>
                    </div>
                    @if($quote->serviceOption)
                    <div class="mb-3">
                        <h5 class="text-primary">Option</h5>
                        <p class="font-weight-bold">
                            {{ $quote->serviceOption->option_name }} 
                            <span class="badge badge-success">@currency($quote->serviceOption->option_price, false, true)</span>
                        </p>
                    </div>
                    @endif
                    <div class="mb-3">
                        <h5 class="text-primary">Detail</h5>
                        <p class="text-muted">{!! $quote->detail !!}</p>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-primary">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
