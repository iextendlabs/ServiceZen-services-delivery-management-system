@extends('site.layout.app')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12">
    <div class="w-full max-w-lg px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="bg-purple-800 text-white px-6 py-4">
                <h3 class="text-center text-lg font-semibold">Payment Details</h3>
            </div>
            <div class="p-6">
                @if (Session::has('error'))
                    <div class="rounded-md bg-red-50 p-4 mb-4 text-center">
                        <p class="text-red-700">{{ Session::get('error') }}</p>
                    </div>
                @endif
                @if (Session::has('success'))
                    <div class="rounded-md bg-green-50 p-4 mb-4 text-center">
                        <p class="text-green-700">{{ Session::get('success') }}</p>
                    </div>
                @endif

                <form role="form" action="{{ route('stripe.post') }}" method="post" id="payment-form">
                    @csrf
                    <div class="mb-4">
                        <label for="card-element" class="block text-sm font-medium text-gray-700">Card Information</label>
                        <div id="card-element" class="mt-2 p-3 rounded-md border border-gray-200 bg-white">
                            <!-- Stripe Element -->
                        </div>
                        <div id="card-errors" role="alert" class="text-red-600 mt-2 text-sm"></div>
                    </div>

                    <div class="mt-6">
                        <button class="w-full inline-flex items-center justify-center px-4 py-2 bg-purple-800 text-white rounded-md hover:bg-purple-950" type="submit" id="pay-button">
                            <span class="spinner-border spinner-border-sm d-none mr-3" role="status" aria-hidden="true"></span>
                            <svg class="hidden animate-spin mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg>
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
@endsection
