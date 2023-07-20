@extends('layouts.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
@section('content')
<div class="row">
    <div class="col-md-12 py-5 text-center">
        <h2>Order</h2>
    </div>
</div>
<div class="container">
    @if ($message = Session::get('success'))
    <div class="alert alert-success">
        <span>{{ $message }}</span>
        <button type="button" class="btn-close float-end"  data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    <div>
        @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Whoops!</strong> There were some problems with your input.<br><br>
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        @can('order-download')
            <button type="button" class="btn btn-primary float-end no-print" onclick="printDiv()"><i class="fa fa-print"></i>Download PDF</button>
        @endcan
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="2">Order Details</td>
            <tr>
                <td>
                    <b>Order ID:</b> #{{ $order->id }} <br><br>
                    <b>Date Added:</b> {{ $order->created_at }} <br><br>
                    <b>Order Status:</b> {{ $order->status }}
                </td>
                <td>
                    <b>Total Amount:</b> @currency($order->total_amount) <br><br>
                    <b>Payment Method:</b> {{ $order->payment_method }}
                </td>
            </tr>
        </table>
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="2">Time Slot and Staff</td>
            <tr>
                <td>
                    <b>Staff:</b>@if($order->staff) {{ $order->staff->user->name }} @endif <br><br>
                    <b>Date:</b> {{ $order->date }}
                </td>
                <td>
                    <b>Time Slot:</b>@if($order->time_slot) {{ $order->time_slot->name }} @endif <br><br>
                    <b>Time:</b>@if($order->time_slot)  {{ date('h:i A', strtotime($order->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($order->time_slot->time_end)) }} @endif
                </td>
            </tr>
        </table>
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="3">Address Details</td>
            <tr>
                <td>
                    <b>Building Name:</b> {{ $order->buildingName }} <br><br>
                    <b>Area:</b> {{ $order->area }}
                </td>
                <td>
                    <b>Flat / Villa:</b> {{ $order->total_amount }} <br><br>
                    <b>Land Mark:</b> {{ $order->landmark }}
                </td>
                <td>
                    <b>Street:</b> {{ $order->street }} <br><br>
                    <b>City:</b> {{ $order->city }}
                </td>
            </tr>
        </table>
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="2">Customer Details</td>
            <tr>
                <td>
                    <b>Name:</b> {{ $order->customer->name }} <br><br>
                    <b>Email:</b> {{ $order->customer->email }}
                </td>
                <td>
                    <b>Phone Number:</b> {{ $order->number }} <br><br>
                    <b>Whatsapp Number:</b> {{ $order->whatsapp }}
                </td>
            </tr>
            <tr>
                <td colspan="3" class="text-left">
                    <b>Location of customer:</b> <a href="https://maps.google.com/maps?q={{ $order->latitude }},+{{ $order->longitude }}" target="_blank">click</a>
                </td>
            </tr>
        </table>
        <table class="table table-bordered album bg-light">
            <td class="text-left" colspan="4">Services Details</td>
            <tr>
                <th>Service Name</th>
                <th>Status</th>
                <th>Duration</th>
                <th class="text-right">Amount</th>
            </tr>
            @foreach($order->orderServices as $orderService)
            <tr>
                <td>{{ $orderService->service->name }}</td>
                <td>{{ $orderService->status }}</td>
                <td>{{ $orderService->service->duration }}</td>
                <td class="text-right">@currency($orderService->price)</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                <td class="text-right">@currency($order->order_total->sub_total)</td>
            </tr>
            @if($order->order_total->transport_charges != 0)
            <tr>
                <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                <td class="text-right">@currency($order->order_total->transport_charges)</td>
            </tr>
            @endif
            @if($order->order_total->staff_charges != 0)
            <tr>
                <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                <td class="text-right">@currency($order->order_total->staff_charges)</td>
            </tr>
            @endif
            <tr>
                <td colspan="3" class="text-right"><strong>Total:</strong></td>
                <td class="text-right">@currency($order->total_amount)</td>
            </tr>
        </table>
        @if(isset($order->staff))
        <fieldset>
            <legend>Staff Commission</legend>
            <table class="table table-bordered album bg-light">
                <tr>
                    <th>Order ID</th>
                    <th>Order Status</th>
                    <th>Staff</th>
                    <th>Order Sub Total</th>
                    <th>Staff Commission Amount</th>
                    <th class="no-print">Action</th>
                </tr>
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="user_id" value="{{ $order->service_staff_id }}">
                    <input type="hidden" name="amount" value="{{ ($order->order_total->sub_total * $order->staff->commission) / 100 }}">
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->status }}</td>
                        <td>{{ $order->staff->user->name }}</td>
                        <td>@currency($order->order_total->sub_total)</td>
                        <td>@currency(($order->order_total->sub_total * $order->staff->commission) / 100)</td>
                        <td class="no-print">
                            @if(empty($order->transactions->status))
                            @can('order-edit')
                            <button type="submit" class="btn btn-primary">Approve</button>
                            @endcan
                            @else
                            <button type="submit" class="btn btn-primary" disabled>Approved</button>
                            @endif
                        </td>
                    </tr>
                </form>
            </table>
        </fieldset>
        @endif
        @if(isset($order->affiliate))
        <fieldset>
            <legend>Affiliate Commission</legend>
            <table class="table table-bordered album bg-light">
                <tr>
                    <th>Order Id</th>
                    <th>Order Amount</th>
                    <th>Affiliate</th>
                    <th>Staff Commission</th>
                    <th class="no-print">Action</th>
                </tr>
                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                    <input type="hidden" name="user_id" value="{{ $order->affiliate->id}}">
                    <input type="hidden" name="amount" value="{{ ($order->total_amount * $order->affiliate->affiliate->commission) / 100 }}">
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>@currency($order->total_amount)</td>
                        <td>{{ $order->affiliate->name }}</td>
                        <td>@currency(($order->total_amount * $order->affiliate->affiliate->commission) / 100)</td>
                        <td class="no-print">
                            @if(empty($order->transaction()[0]->status))
                            @can('order-edit')
                            <button type="submit" class="btn btn-primary">Approve</button>
                            @endcan
                            @else
                            <button type="submit" class="btn btn-primary" disabled>Approved</button>
                            @endif
                        </td>
                    </tr>
                </form>
            </table>
        </fieldset>
        @endif
    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
    </script>
@endsection