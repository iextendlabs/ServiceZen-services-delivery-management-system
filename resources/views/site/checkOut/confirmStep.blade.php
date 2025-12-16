@extends('site.layout.app')
@section('content')

<div class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-semibold text-gray-800">Confirm Order</h2>
        </div>

        @if(Session::has('error'))
            <div class="rounded-md bg-red-50 p-4 mb-4">
                <p class="text-red-700">{{ Session::get('error') }}</p>
            </div>
        @endif
        @if(Session::has('success'))
            <div class="rounded-md bg-green-50 p-4 mb-4">
                <p class="text-green-700">{{ Session::get('success') }}</p>
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-md bg-red-50 p-4 mb-4">
                <p class="font-semibold text-red-700">Whoops! There were some problems with your input.</p>
                <ul class="mt-2 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h5 class="text-lg font-medium mb-4">Services</h5>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-sm text-gray-600">#</th>
                            <th class="px-4 py-2 text-sm text-gray-600">Image</th>
                            <th class="px-4 py-2 text-sm text-gray-600">Name</th>
                            <th class="px-4 py-2 text-sm text-gray-600">Duration</th>
                            <th class="px-4 py-2 text-sm text-gray-600">Price</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($services as $key=>$service)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-700">{{ ++$key }}</td>
                                <td class="px-4 py-3"><img src="service-images/{{ $service->image }}" alt class="h-14 w-14 rounded-md border" /></td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $service->name }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $service->duration }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">
                                    @if(isset($service->discount))
                                        @currency( $service->discount )
                                    @else
                                        @currency( $service->price )
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h5 class="text-lg font-medium mb-4">Booking Details</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <h6 class="text-sm text-gray-500">Address</h6>
                    <div class="mt-2 text-sm text-gray-700">
                        <div><strong>Building Name:</strong> {{ $order->buildingName }}</div>
                        <div><strong>Flat/Villa:</strong> {{ $order->flatVilla }}</div>
                        <div><strong>Street:</strong> {{ $order->street }}</div>
                        <div><strong>District:</strong> {{ $order->district }}</div>
                        <div><strong>Area:</strong> {{ $order->area }}</div>
                        <div><strong>City:</strong> {{ $order->city }}</div>
                        <div><strong>Number:</strong> {{ $order->number }}</div>
                    </div>
                </div>
                <div>
                    <h6 class="text-sm text-gray-500">Time Slots And Staff</h6>
                    <div class="mt-2 text-sm text-gray-700">
                        <div><strong>Time Slot:</strong> {{ $order->time_slot_value }}</div>
                        <div><strong>Staff:</strong> {{ $order->staff_name }}</div>
                        <div><strong>Date:</strong> {{ $order->date }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <h5 class="text-lg font-medium mb-4">Payment Summary</h5>
            <div class="space-y-2 text-sm text-gray-700 max-w-md mx-auto">
                <div class="flex justify-between"><span>Service Total:</span><span>@currency($order->order_total->sub_total)</span></div>
                <div class="flex justify-between"><span>Coupon Discount:</span><span>@currency( '-'.$order->order_total->discount ? '-'.$order->order_total->discount : 0)</span></div>
                <div class="flex justify-between"><span>Staff Charges:</span><span>@currency( $order->order_total->transport_charges ? $order->order_total->transport_charges : 0)</span></div>
                <div class="flex justify-between"><span>Transport Charges:</span><span>@currency( $order->order_total->staff_charges ? $order->order_total->staff_charges : 0)</span></div>
                <div class="flex justify-between font-semibold text-gray-800"><span>Total:</span><span>@currency($order->total_amount)</span></div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6">
            <form action="{{ route('order.store') }}" method="POST">
                @csrf
                <input type="hidden" name="total_amount" value="{{ $order->total_amount }}"/>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Comment</label>
                    <textarea name="order_comment" class="mt-1 block w-full rounded-md border-gray-200 shadow-sm" cols="30" rows="5"></textarea>
                </div>
                <div class="flex flex-col md:flex-row md:space-x-4 items-center justify-center">
                    <button type="submit" class="inline-flex items-center px-6 py-2 bg-purple-800 text-white rounded-md hover:bg-purple-950">Confirm Order</button>
                    <a href="/bookingStep" class="mt-3 md:mt-0">
                        <button type="button" class="inline-flex items-center px-5 py-2 bg-gray-200 text-gray-700 rounded-md">Edit Order</button>
                    </a>
                    <a href="/" class="mt-3 md:mt-0">
                        <button type="button" class="inline-flex items-center px-5 py-2 bg-green-600 text-white rounded-md">Continue Shopping</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection