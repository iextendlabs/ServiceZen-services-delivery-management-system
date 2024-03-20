<!-- TODO Change edit dropdown to icon -->

<table class="table-striped table-bordered table-responsive table">
    <tr>
        <th>Sr#</th>
        <th>Order#</th>
        <th>Staff</th>
        <th><i class="fas fa-clock"></i> Appointment Date</th>
        <th><i class="fas fa-clock"></i> Slots</th>

        @if (auth()->user()->getRoleNames() == '["Supervisor"]')
        <th>Landmark</th>
        <th>Area</th>
        <th>City</th>
        <th>Building name</th>
        @else
        <th>Customer</th>
        <th>Total Amount</th>
        <th>Payment Method</th>
        <th>Comment</th>
        @endif
        <th>Status</th>

        <th>Date Added</th>
        <th style="min-width:120px">Action</th>
    </tr>
    @if (count($orders))
    @foreach ($orders as $order)
    <tr>
        <td>{{ ++$i }}</td>
        <th>
            @can('order-view')<a href="{{ route('orders.show', $order->id) }}">@endcan
                #{{ $order->id }}
                @can('order-view')</a>@endcan
        </th>
        <td><i class="fas fa-user"></i> {{ $order->staff_name }}<br>
            <i class="fas fa-car"></i> {{ $order->driver ? $order->driver->name : 'N/A' }}</td>
        <td>{{ $order->date }}</td>
        <td>{{ $order->time_slot_value }}</td>
        @if (auth()->user()->getRoleNames() == '["Supervisor"]')
        <td>{{ $order->landmark }}</td>
        <td>{{ $order->area }}</td>
        <td>{{ $order->city }}</td>
        <td>{{ $order->buildingName }}</td>
        @else
        <td>@if($order->customer_id)<a href="{{ route('customers.show',$order->customer_id) }}">{{ $order->customer_name }}</a>@else {{ $order->customer_name }} @endif</td>
        <td>@currency($order->total_amount)</td>
        <td>{{ $order->payment_method }}</td>
        <td>@if(isset($order->order_comment)){{ substr($order->order_comment, 0, 50) }}... @endif</td>
        @endif
        <td style="min-width:150px">{{ $order->status }} <br>
            <i class="fas fa-car"></i>{{ $order->driver_status }}
            @can('cash-collection-edit')
            @if(($order->cashCollection)) <br><br>
            <a href="{{ route('cashCollection.index') }}?order_id={{$order->id}}">
                <i class="fas fa-money-bill"></i> {{ $order->cashCollection->status }}
            </a>
            @endif
            @endcan
            @if($order->status == 'Complete') <br><br>
            @if(auth()->user()->getRoleNames() != '["Supervisor"]')
            @if(!$order->cashCollection)
            <a href="{{ route('cashCollection.create',$order->id) }}">
                <i class="fas fa-money-bill"></i> Create
            </a>

            @endif
            @endif
            @endif
        </td>
        <td>{{ $order->created_at }}</td>
        <td>
            @can('order-edit')
            <!-- <a class="btn btn-primary" href="{{ route('orders.edit', $order->id) }}">
                            <i class="fas fa-edit"></i>
                        </a> -->
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a id="navbarDropdown" class=" btn btn-primary" href="#" data-bs-toggle="dropdown">
                        <i class="fas fa-bars"></i>
                    </a>

                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        @can('order-booking-edit')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=booking">Booking Edit</a>
                        @endcan
                        @can('order-status-edit')
                        @if(auth()->user()->getRoleNames() == '["Supervisor"]' && $order->status == 'Pending')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                        @elseif(auth()->user()->getRoleNames() != '["Supervisor"]')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                        @endif
                        @endcan
                        @can('order-detail-edit')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=address">Address Edit</a>
                        @endcan
                        @can('order-affiliate-edit')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=affiliate">Affiliate Edit</a>
                        @endcan
                        @can('order-comment-edit')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=comment">Comment Edit</a>
                        @endcan
                        @can('order-driver-status-edit')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=driver">Driver Edit</a>
                        @endcan
                        @can('order-driver-status-edit')
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=order_driver_status">Order Driver Status Edit</a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=custom_location">Add Custom Location</a>
                        @can('order-chat')
                        <a class="dropdown-item" href="{{ route('orders.chat', $order->id) }}">Chat</a>
                        @endcan
                        <a class="dropdown-item" href="{{ route('orders.edit', $order->id) }}?edit=services">Edit Services</a>
                    </div>
                </li>
            </ul>
            @endcan
            <form id="deleteForm{{ $order->id }}" action="{{ route('orders.destroy', $order->id) }}" method="POST">
                @csrf
                @method('DELETE')
                @can('order-delete')
                <button type="button" onclick="confirmDelete('{{ $order->id }}')" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                </button>
                @endcan
            </form>
            @if ($order->status !== 'Complete' && Auth::User()->getRoleNames() == '["Staff"]')
            @if ($order->status == 'Confirm')
            <a class="btn btn-sm btn-success" href="{{ route('updateOrderStatus', $order->id) }}?status=Accepted">
                <i class="fas fa-thumbs-up"></i>
            </a>

            <a class="btn btn-sm btn-danger" href="{{ route('updateOrderStatus', $order->id) }}?status=Rejected">
                <i class="fas fa-thumbs-down"></i>
            </a>
            @endif
            @if ($order->status == 'Accepted')
            <a class="btn btn-sm btn-success" href="{{ route('updateOrderStatus', $order->id) }}?status=Complete"><i class="fas fa-check-circle"></i></a>
            @endif
            @endif

        </td>
    </tr>
    @endforeach
    @else
    <tr>
        <td colspan="11" class="text-center"> There is no Order</td>
    </tr>
    @endif
</table>
<script>
    function confirmDelete(Id) {
        var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
</script>