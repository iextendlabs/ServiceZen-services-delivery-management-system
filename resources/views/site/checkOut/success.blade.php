@extends('site.layout.app')
@section('content')
    <div class="album bg-light">
        <div class="container">
            <div>
                <section class="jumbotron text-center">
                    <div class="container">
                        <h1 class="jumbotron-heading">Your order has been placed!</h1>
                    </div>
                </section>
                <div class="album py-5 bg-light">
                    <div class="container">
                        <li>Your order has been successfully processed!</li>
                        <li>We have send you email with your login credentials.</li>
                        <li>Visit our website for your order detail and book more service</li>
                        @auth
                            <li>You can view your order history by clicking on <a href="/order">Order History</a>.</li>
                        @endauth
                        <li>Please direct any questions you have to the store owner.</li>
                        <li>Thanks for booking our service!</li>
                        <div class="col-md-12 text-right">
                            <a href="/">
                                <button type="button" class="btn btn-primary">Continue</button>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
