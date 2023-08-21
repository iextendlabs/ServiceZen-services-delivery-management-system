<!doctype html>
<html lang="en">

<head>
    <base href="{{ env('APP_URL') }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ env('APP_NAME') }}</title>

    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
    <style>
        .table {
            margin-bottom: 0px !important;
        }
    </style>
</head>

<body>
    <main role="main">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h4>Your Order Has Been Successfully Placed.</h4>
                </div>
            </div>
            <div>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="2"><i class="fas fa-shopping-cart"></i> Order Details</td>
                    <tr>
                        <td>
                            <b>Order ID:</b> #{{ $data['order']->id }} <br><br>
                            <b>Date Added:</b> {{ $data['order']->created_at }} <br><br>
                            <b>Order Status:</b> {{ $data['order']->status }}
                        </td>
                        <td>
                            <b>Total Amount:</b> @currency( $data['order']->total_amount ) <br><br>
                            <b>Payment Method:</b> {{ $data['order']->payment_method }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="3"><i class="fas fa-clock"></i> Appointment Details</td>
                    <tr>
                        <td>
                            <b>Staff:</b> {{ $data['order']->staff_name }}

                        </td>
                        <td>
                            <b>Date:</b> {{ $data['order']->date }}
                        </td>
                        <td>
                            <b>Time:</b> {{ date('h:i A', strtotime($data['order']->time_slot->time_start)) }} -- {{ date('h:i A', strtotime($data['order']->time_slot->time_end)) }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="3">Address Details</td>
                    <tr>
                        <td>
                            <b>Building Name:</b> {{ $data['order']->buildingName }} <br><br>
                            <b>Area:</b> {{ $data['order']->area }}
                        </td>
                        <td>
                            <b>Flat / Villa:</b> {{ $data['order']->total_amount }} <br><br>
                            <b>Land Mark:</b> {{ $data['order']->landmark }}
                        </td>
                        <td>
                            <b>Street:</b> {{ $data['order']->street }} <br><br>
                            <b>City:</b> {{ $data['order']->city }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="2">Customer Details</td>
                    <tr>
                        <td>
                            <b>Name:</b> {{ $data['order']->customer->name }} <br><br>
                            <b>Email:</b> {{ $data['order']->customer->email }}
                        </td>
                        <td>
                            <b>Phone Number:</b> {{ $data['order']->number }} <br><br>
                            <b>Whatsapp Number:</b> {{ $data['order']->whatsapp }}
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="4"><i class="fas fa-spa"></i> Services Details</td>
                    <tr>
                        <th>Service Name</th>
                        <th>Status</th>
                        <th>Duration</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    @foreach($data['order']->orderServices as $orderService)
                    <tr>
                        <td>{{ $orderService->service->name }}</td>
                        <td>{{ $orderService->status }}</td>
                        <td>{{ $orderService->service->duration }}</td>
                        <td class="text-right">@currency($orderService->price)</td>
                    </tr>
                    @endforeach

                    <tr>
                        <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                        <td class="text-right">@currency($data['order']->order_total->sub_total)</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Coupon Discount:</strong></td>
                        <td class="text-right">{{ config('app.currency') }}{{ $data['order']->order_total->discount ? '-'.$data['order']->order_total->discount : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                        <td class="text-right">{{ config('app.currency') }}{{ $data['order']->order_total->transport_charges ? $data['order']->order_total->transport_charges : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                        <td class="text-right">{{ config('app.currency') }}{{ $data['order']->order_total->staff_charges ? $data['order']->order_total->staff_charges : 0 }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="text-right"><strong>Total:</strong></td>
                        <td class="text-right">@currency($data['order']->total_amount)</td>
                    </tr>
                </table>
                @if($data['order']->order_comment)
                <table class="table table-striped table-bordered album bg-light">
                    <th class="text-left" colspan="4">Order Comment</th>
                    <tr>
                        <td class="text-left">{!! nl2br($data['order']->order_comment) !!}</td>
                    </tr>
                </table>
                @endif
            </div><br>
            @if( empty($data['password']))
            <p>We have create your user, you can visit our website to see your order detail</p>
            <p>Your Login credentials are.</p>
            <p> <b>Name:</b> {{ $data['name'] }}</p>
            <p><b>Email:</b> {{ $data['email'] }}</p>
            <p><b>Password:</b> {{ $data['password'] }}</p>
            @else
            <p>You have Customer account, you can visit our website to see your order detail</p>
            <p>Your Login credentials are.</p>
            <p> <b>Name:</b> {{ $data['name'] }}</p>
            <p><b>Email:</b> {{ $data['email'] }}</p>
            @endif
        </div>
    </main>
</body>

</html>