@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-4 shadow-sm border-0">
                    <div class="card-header bg-light font-weight-bold">
                        Quote Details
                    </div>
                    <div class="card-body">
                        <!-- User Info -->
                        <h6><strong>Send by {{ $quote->user->name ?? 'N/A' }}</strong></h6>
                        @if ($quote->phone)
                            <p class="mb-1"><i class="fas fa-phone fa-sm text-success"></i> {{ $quote->phone }}</p>
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
                                class="rounded shadow-sm img-fluid mb-3" style="max-width: 100px;">
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
        @if (auth()->user()->hasRole('Admin') && count($quote->staffs) > 0)
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
                            <td>{{ $staff->name ?? '' }}</td>
                            <td>{{ $staff->pivot->status ?? '' }}</td>
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
@endsection
