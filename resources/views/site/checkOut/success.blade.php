@extends('site.layout.app')
@section('content')
    <div class="min-h-screen bg-gray-50 flex items-center justify-center py-12">
        <div class="max-w-3xl w-full bg-white shadow-md rounded-lg p-8">
            <div class="text-center">
                <h1 class="text-2xl font-semibold text-gray-800">Your order has been placed!</h1>
                <p class="mt-2 text-sm text-gray-500">We've emailed your order details and next steps.</p>
            </div>

            <ul class="mt-6 list-disc list-inside space-y-2 text-gray-700">
                <li>Your order has been successfully processed!</li>
                <li>We have sent you an email with your login credentials.</li>
                <li>Visit our website for your order details and to book more services.</li>
                @auth
                    <li>You can view your order history by clicking on <a class="text-purple-800 underline" href="/order">Order History</a>.</li>
                @endauth
                <li>Please direct any questions you have to the store owner.</li>
                <li>Thanks for booking our service!</li>
            </ul>

            <div class="mt-6 text-right">
                <a href="/" class="inline-flex items-center px-5 py-2 bg-purple-800 text-white rounded-md hover:bg-purple-950">Continue</a>
            </div>
        </div>
    </div>
@endsection
