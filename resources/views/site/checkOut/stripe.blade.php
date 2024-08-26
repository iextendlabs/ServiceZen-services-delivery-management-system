@extends('site.layout.app')
@section('content')
    <div class="container" style="margin-top: 50px">
        <div class="row justify-content-center">
            <div class="col-8 offset-1">
                <div class="card credit-card-box">
                    <div class="card-header">
                        <h3 class="card-title">Payment Details</h3>
                    </div>
                    <div class="card-body">
                        @if (Session::has('error'))
                            <div class="alert alert-danger text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('error') }}</p>
                            </div>
                        @endif
                        @if (Session::has('success'))
                            <div class="alert alert-success text-center">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                                <p>{{ Session::get('success') }}</p>
                            </div>
                        @endif

                        <form role="form" action="{{ route('stripe.post') }}" method="post" class="require-validation"
                            data-cc-on-file="false" data-stripe-publishable-key="{{ env('STRIPE_KEY') }}" id="payment-form">
                            @csrf
                            <div class='form-row'>
                                <div class='col-12 form-group required'>
                                    <label>Card Number</label>
                                    <input autocomplete='off' class='form-control card-number' maxlength="20" type='text' required>
                                </div>
                            </div>

                            <div class='form-row'>
                                <div class='col-4 form-group cvc required'>
                                    <label>CVC</label>
                                    <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' maxlength="4" type='text' required>
                                </div>
                                <div class='col-4 form-group expiration required'>
                                    <label>Expiration Month</label>
                                    <input class='form-control card-expiry-month' placeholder='MM' maxlength="2" type='text' required>
                                </div>
                                <div class='col-4 form-group expiration required'>
                                    <label>Expiration Year</label>
                                    <input class='form-control card-expiry-year' placeholder='YYYY' maxlength="4" type='text' required>
                                </div>
                            </div>

                            <div class='form-row'>
                                <div class='col-12 error form-group d-none'>
                                    <div class='alert alert-danger'>Please correct the errors and try again.</div>
                                </div>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-6">
                                    <button class="btn btn-primary btn-block" type="submit">Pay Now</button>
                                </div>
                                <div class="col-6">
                                    <a class="btn btn-danger btn-block" type="button" href="bookingStep">Cancel</a>
                                </div>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            var $form = $(".require-validation");

            $('form.require-validation').on('submit', function(e) {
                var $form = $(".require-validation"),
                    inputSelector = ['input[type=text]', 'input[type=file]', 'textarea'].join(', '),
                    $inputs = $form.find('.required').find(inputSelector),
                    $errorMessage = $form.find('div.error'),
                    valid = true;

                $errorMessage.addClass('d-none');
                $('.has-error').removeClass('has-error');
                $inputs.each(function(i, el) {
                    var $input = $(el);
                    if ($input.val() === '') {
                        $input.parent().addClass('has-error');
                        $errorMessage.removeClass('d-none');
                        e.preventDefault();
                    }
                });

                if (!$form.data('cc-on-file')) {
                    e.preventDefault();
                    Stripe.setPublishableKey($form.data('stripe-publishable-key'));
                    Stripe.createToken({
                        number: $('.card-number').val(),
                        cvc: $('.card-cvc').val(),
                        exp_month: $('.card-expiry-month').val(),
                        exp_year: $('.card-expiry-year').val()
                    }, stripeResponseHandler);
                }
            });

            function stripeResponseHandler(status, response) {
                if (response.error) {
                    $('.error')
                        .removeClass('d-none')
                        .find('.alert')
                        .text(response.error.message);
                } else {
                    var token = response['id'];
                    $form.find('input[type=text]').empty();
                    $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
                    $form.get(0).submit();
                }
            }
        });
    </script>
@endsection
