@extends('site.layout.app')

@section('content')
    <div class="bg-gray-50 py-12">
        <div class="max-w-5xl mx-auto px-4">
            <div class="text-center mb-8">
                <h2 class="text-2xl font-semibold text-gray-800">Your Booked Service</h2>
            </div>

            @if (Session::has('error'))
                <div class="rounded-md bg-red-50 p-4 mb-4">
                    <p class="text-red-700">{{ Session::get('error') }}</p>
                </div>
            @endif
            @if (Session::has('success'))
                <div class="rounded-md bg-green-50 p-4 mb-4">
                    <p class="text-green-700">{{ Session::get('success') }} — <a class="underline text-purle-800" href="/">Continue</a></p>
                </div>
            @endif

            @if (count($formattedBookings) != 0)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-sm text-gray-600">Image</th>
                                <th class="px-4 py-3 text-sm text-gray-600">Service Name</th>
                                <th class="px-4 py-3 text-sm text-gray-600">Price / Duration</th>
                                <th class="px-4 py-3 text-sm text-gray-600">Booking Detail</th>
                                <th class="px-4 py-3 text-sm text-gray-600">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($formattedBookings as $key => $booking)
                                <tr>
                                    <td class="px-4 py-3"><a href="/service/{{ $booking['service']->slug }}"><img src="service-images/{{ $booking['service']->image }}" class="h-14 w-14 rounded-md border" alt></a></td>
                                    <td class="px-4 py-3 text-sm text-gray-800"><a class="hover:underline" href="/service/{{ $booking['service']->slug }}">{{ $booking['service']->name }}</a></td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @if ($booking['option_total_price'] > 0)
                                            <div>@currency($booking['option_total_price'], false, true)</div>
                                        @else
                                            <div>
                                                @if (isset($booking['service']->discount))
                                                    @currency($booking['service']->discount, false, true)
                                                @else
                                                    @currency($booking['service']->price, false, true)
                                                @endif
                                            </div>
                                        @endif
                                        <div class="text-sm text-gray-500 mt-1">
                                            @if ($booking['option_total_duration'] != null)
                                                {{ $booking['option_total_duration'] }}
                                            @elseif($booking['service']->duration)
                                                {{ $booking['service']->duration }}
                                            @endif
                                        </div>
                                        @if (count($booking['option']) != 0)
                                            <div class="text-sm text-gray-500 mt-2">
                                                @foreach ($booking['option'] as $option)
                                                    <div>{{ $option->option_name }}</div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">
                                        <div><i class="fa fa-calendar mr-2"></i>{{ $booking['date'] }}</div>
                                        <div><i class="fa fa-user mr-2"></i>{{ $booking['staff'] }}</div>
                                        <div><i class="fa fa-clock mr-2"></i>{{ $booking['slot'] }}</div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <a href="/removeToCart/{{ $booking['service']->id }}" class="inline-flex items-center px-3 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100"><i class="fa fa-times-circle mr-2"></i>Remove</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="inline-flex flex-col items-center">
                        <svg class="w-24 h-24 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3h18v4H3V3zM5 11h14v9H5v-9z"></path></svg>
                        <h3 class="mt-6 text-xl font-semibold text-gray-800">Your cart is empty</h3>
                        <p class="mt-2 text-sm text-gray-500">Explore our services and add your first booking.</p>
                        <a href="/" class="mt-4 inline-flex items-center px-5 py-2 bg-purple-800 text-white rounded-md hover:bg-purple-950">Browse Services</a>
                    </div>
                </div>
            @endif

            @if (count($formattedBookings))
                <div class="text-center mt-6">
                    <a href="bookingStep" class="inline-flex items-center px-6 py-2 bg-purple-800 text-white rounded-md hover:bg-purple-950">Checkout</a>
                </div>
            @endif
        </div>
    </div>
@endsection
