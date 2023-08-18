<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Order Print</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        @page  {
            margin: 0;
        }
        .table td,
        .table th {
            vertical-align: middle;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Cash Collection</h2>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>SR#</th>
                        <th>Order#</th>
                        <th>Staff</th>
                        <th>Collected Amount</th>
                        <th>Customer</th>
                        <th>Order Total</th>
                        <th>Description</th>
                        <th>Order Comment</th>
                        <th>Status</th>
                    </tr>
                    @if(count($cash_collections))
                    @foreach ($cash_collections as $key => $cash_collection)
                    <tr>
                        <td>{{ ++$key }}</td>
                        <td>{{ $cash_collection->order_id }}</td>
                        <td>{{ $cash_collection->staff_name }}</td>
                        <td>@currency($cash_collection->amount)</td>
                        <td>{{ $cash_collection->order->customer->name }}</td>
                        <td>@currency( $cash_collection->order->total_amount )</td>
                        <td>{{ $cash_collection->description }}</td>

                        <td> {{$cash_collection->order->comment}}</td>

                        <td>{{ $cash_collection->status }}</td>

                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="8" class="text-center"> There is no Order</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script>
    window.onload = function() {
        window.print();
    };
</script>

</html>