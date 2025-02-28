@extends('site.layout.app')
@section('content')
<div class="d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="col-md-6">
        <div class="card shadow-lg">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0 text-center">Payment Details</h3>
            </div>
            <div class="card-body">
                @if (Session::has('error'))
                    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                        <p>{{ Session::get('error') }}</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif
                @if (Session::has('success'))
                    <div class="alert alert-success alert-dismissible fade show text-center" role="alert">
                        <p>{{ Session::get('success') }}</p>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                @endif

                <form role="form" action="{{ route('stripe.post') }}" method="post" id="payment-form">
                    @csrf
                    <div class="form-group">
                        <label for="card-element" class="font-weight-bold">Card Information</label>
                        <div id="card-element" class="form-control p-3" style="border-radius: 8px; border: 1px solid #ddd;">
                            <!-- Stripe Element -->
                        </div>
                        <div id="card-errors" role="alert" class="text-danger mt-2"></div>
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
</div>

<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('{{ env('STRIPE_KEY') }}');
    var elements = stripe.elements();

    var style = {
        base: {
            color: '#32325d',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            fontSize: '16px',
            '::placeholder': {
                color: '#aab7c4'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    };

    var card = elements.create('card', { style: style });
    card.mount('#card-element');

    card.on('change', function(event) {
        var displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    var form = document.getElementById('payment-form');
    form.addEventListener('submit', function(event) {
        event.preventDefault();

        var payButton = document.getElementById('pay-button');
        payButton.disabled = true;
        payButton.querySelector('.spinner-border').classList.remove('d-none');
        payButton.querySelector('.btn-text').classList.add('opacity-50');

        stripe.createToken(card).then(function(result) {
            if (result.error) {
                payButton.disabled = false;
                payButton.querySelector('.spinner-border').classList.add('d-none');
                payButton.querySelector('.btn-text').classList.remove('opacity-50');

                var errorElement = document.getElementById('card-errors');
                errorElement.textContent = result.error.message;
            } else {
                stripeTokenHandler(result.token);
            }
        });
    });

    function stripeTokenHandler(token) {
        var form = document.getElementById('payment-form');
        var hiddenInput = document.createElement('input');
        hiddenInput.setAttribute('type', 'hidden');
        hiddenInput.setAttribute('name', 'stripeToken');
        hiddenInput.setAttribute('value', token.id);
        form.appendChild(hiddenInput);
        form.submit();
    }
</script>
<style>
    .card {
        border-radius: 12px;
        overflow: hidden;
    }
    .card-header {
        border-radius: 12px 12px 0 0;
    }
    .btn-primary {
        background-color: #4a90e2;
        border: none;
        transition: background-color 0.3s ease;
    }
    .btn-primary:hover {
        background-color: #357abd;
    }
    .btn-primary:disabled {
        background-color: #4a90e2;
        opacity: 0.7;
    }
    #card-element {
        transition: border-color 0.3s ease;
    }
    #card-element:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 2px rgba(74, 144, 226, 0.2);
    }
    .opacity-50 {
        opacity: 0.5;
    }
</style>
@endsection
