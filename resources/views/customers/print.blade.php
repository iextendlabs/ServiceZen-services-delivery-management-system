<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Customer Print</title>

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
            <div class="col-md-12">
                <div class="text-center">
                    <h2>Customer</h2>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered ">
                    <tr>
                        <th>SR#</th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Addresses</th>
                        <th>District</th>
                        <th>Number</th>
                        <th>Whatsapp</th>
                        <th>Date Added</th>
                        <th>
                            Affiliate Name <br>
                            Code
                        </th>
                        <th>Coupons</th>
                    </tr>
                    @if(count($customers))
                    @foreach ($customers as $key => $customer)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $customer->id }}</td>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>@if($customer->status == 1) Enabled @else Disabled @endif</td>
                        <td>
                            @foreach($customer->customerProfiles as $customerProfile)
                                <div>
                                    {{ $customerProfile->buildingName ?? '' }} {{ $customerProfile->flatVilla ?? '' }},
                                    {{ $customerProfile->street ?? '' }},
                                    {{ $customerProfile->area ?? '' }},
                                    {{ $customerProfile->city ?? '' }}
                                </div>
                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        </td>
                        <td>
                            @foreach($customer->customerProfiles as $customerProfile)
                                <div>{{ $customerProfile->district ?? '' }}</div>
                                @if(!$loop->last)
                                    <hr>
                                @endif
                            @endforeach
                        </td>
                        <td>{{optional($customer->customerProfiles->first())->number }}</td>
                        <td>{{ optional($customer->customerProfiles->first())->whatsapp }}</td>
                        <td>{{ $customer->created_at }}</td>
                        <td>
                            {{ $customer->userAffiliate->affiliateUser->name ?? "" }} <br>
                            {{ $customer->userAffiliate->affiliate->code ?? ""}}
                        </td>
                        <td>{{ implode(",", $customer->coupons->pluck('code')->toArray()) }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                        <td colspan="7" class="text-center"> There is no customers</td>
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