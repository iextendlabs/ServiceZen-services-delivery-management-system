<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="paymentModalLabel">Enter Payment Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Alert Messages -->
                <div id="alert-container"></div>

                <form id="payment-form" action="{{ route('stripe.staffDepositPost') }}" method="post">
                    @csrf
                    <div class="form-group">
                        <label for="amount" class="font-weight-bold">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">AED</span>
                            </div>
                            <input type="number" class="form-control" name="amount" id="amount"
                                placeholder="Enter amount" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="card-element" class="font-weight-bold">Card Information</label>
                        <div id="card-element" class="form-control pt-2 pb-2"></div>
                        <div id="card-errors" class="text-danger mt-2"></div>
                    </div>

                    <div class="text-center mt-4">
                        <button class="btn btn-primary btn-lg w-100" type="submit" id="pay-button">
                            <span class="spinner-border spinner-border-sm d-none" role="status"
                                aria-hidden="true"></span>
                            <span class="btn-text">Pay Now</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
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

<!-- Styles -->
<style>
    .modal-content {
        border-radius: 12px;
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
        opacity: 0.7;
    }

    .opacity-50 {
        opacity: 0.5;
    }
</style>
