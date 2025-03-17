@extends($app ? 'site.layout.app_layout' : 'layouts.app')
@section('content')
    @if ($app)
        <div class="container d-flex justify-content-center align-items-center" style="min-height: 94vh !important;">
        @else
            <div class="container d-flex justify-content-center">
    @endif
    <div class="row border border-1 py-4 bg-white shadow rounded">
        <div class="col-md-12">
            <div class="text-center">
                <h3>Enter Payment Details</h3>
            </div>
        </div>
        <div class="col-md-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">
                    <span>{{ $message }}</span>
                    <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form id="payment-form" action="{{ route('stripe.post') }}" method="post">
                <input type="hidden" name="user_id" value="{{ $user_id }}">
                <input type="hidden" name="plan_id" value="{{ $plan_id ?? null }}">
                @csrf
                <div class="form-group">
                    <label for="amount" class="font-weight-bold">Amount</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">AED</span>
                        </div>
                        <input type="number" class="form-control" name="amount" id="amount" value="{{ $amount ?? null }}" placeholder="Enter amount"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="card-element" class="font-weight-bold">Card Information</label>
                    <div id="card-element" class="form-control pt-2 pb-2"></div>
                    <div id="card-errors" class="text-danger mt-2"></div>
                </div>

                <div class="text-center mt-4">
                    <button class="btn btn-primary btn-lg w-100" type="submit" id="pay-button">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text">Pay Now</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
        $(document).ready(function() {
            var stripe = Stripe('{{ env('STRIPE_KEY') }}');
            var elements = stripe.elements();
            var card = elements.create('card', {
                style: {
                    base: {
                        fontSize: '16px',
                        color: '#32325d',
                        '::placeholder': {
                            color: '#aab7c4'
                        }
                    },
                    invalid: {
                        color: '#fa755a'
                    }
                }
            });
            card.mount('#card-element');

            card.on('change', function(event) {
                if (event.error) {
                    $('#card-errors').text(event.error.message);
                } else {
                    $('#card-errors').text('');
                }
            });

            $('#payment-form').submit(function(e) {
                e.preventDefault();

                var $payButton = $('#pay-button');
                $payButton.prop('disabled', true);
                $payButton.find('.spinner-border').removeClass('d-none');
                $payButton.find('.btn-text').addClass('opacity-50');

                stripe.createToken(card).then(function(result) {
                    if (result.error) {
                        $('#card-errors').text(result.error.message);
                        resetButton();
                    } else {
                        $('<input>', {
                            type: 'hidden',
                            name: 'stripeToken',
                            value: result.token.id
                        }).appendTo('#payment-form');

                        $('#payment-form')[0].submit();
                    }
                });
            });

            function resetButton() {
                var $payButton = $('#pay-button');
                $payButton.prop('disabled', false);
                $payButton.find('.spinner-border').addClass('d-none');
                $payButton.find('.btn-text').removeClass('opacity-50');
            }
        });
    </script>
@endsection
