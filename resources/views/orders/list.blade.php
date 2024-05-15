<!-- TODO Change edit dropdown to icon -->
<table class="table-striped table-bordered table-responsive table">
    <tr>
        <th>Sr#</th>

        <th class="text-left"><a
                href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'id', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Order#</a>
            @if (request('sort') === 'id')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
        </th>
        <th class=""><a
                href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'staff_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Staff</a>
                @if (request('sort') === 'staff_name')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
        </th>
        <th class="fas fa-clock"><a
                href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'date', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Appointment
                Date</a>

            @if (request('sort') === 'date')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
        </th>
        <th class=""><a
                href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'time_slot_value', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Slot
            </a>
            @if (request('sort') === 'time_slot_value')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
        </th>

        @if (auth()->user()->hasRole('Supervisor'))
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'landmark', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Landmark
                </a>
                @if (request('sort') === 'landmark')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif

            </th>
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'area', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Area
                </a>
                @if (request('sort') === 'area')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'city', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">City
                </a>
                @if (request('sort') === 'city')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'buildingName', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Building
                    name
                </a>
                @if (request('sort') === 'buildingName')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
        @else
            <th>
                <a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'customer_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">
                    Customer
                </a>
                @if (request('sort') === 'customer_name')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'total_amount', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Total
                    Amount
                </a>
                @if (request('sort') === 'total_amount')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'payment_method', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Payment
                    Method
                </a>
                @if (request('sort') === 'payment_method')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
            <th class=""><a
                    href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'order_comment', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Comment
                </a>
                @if (request('sort') === 'order_comment')
                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
            @endif
            </th>
        @endif
        <th class=""><a
                href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status
            </a>
            @if (request('sort') === 'status')
            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
        @endif
        </th>
        <th class=""><a
                href="{{ route('orders.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Date
                Added
            </a>
            @if (request('sort') === 'created_at')
            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
        @endif
        </th>
        <th style="min-width:120px">Action</th>
    </tr>
    @if (count($orders))
        @foreach ($orders as $order)
            <tr>
                <td>{{ ++$i }}</td>
                <th>
                    @can('order-view')
                        <a href="{{ route('orders.show', $order->id) }}">
                        @endcan
                        #{{ $order->id }}
                        @can('order-view')
                        </a>
                    @endcan
                </th>
                <td><i class="fas fa-user"></i> {{ $order->staff_name }}<br>
                    <i class="fas fa-car"></i> {{ $order->driver ? $order->driver->name : 'N/A' }}
                </td>
                <td>{{ $order->date }}</td>
                <td>{{ $order->time_slot_value }}</td>
                @if (auth()->user()->hasRole('Supervisor'))
                    <td>{{ $order->landmark }}</td>
                    <td>{{ $order->area }}</td>
                    <td>{{ $order->city }}</td>
                    <td>{{ $order->buildingName }}</td>
                @else
                    <td>
                        @if ($order->customer_id)
                            <a
                                href="{{ route('customers.show', $order->customer_id) }}">{{ $order->customer_name }}</a>
                        @else
                            {{ $order->customer_name }}
                        @endif
                        <br>
                        {{ $order->customer->customerProfile->number ?? '' }}
                        <br>
                        {{ $order->customer->customerProfile->whatsapp ?? '' }}
                    </td>
                    <td>@currency($order->total_amount)</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>
                        @if (isset($order->order_comment))
                            {{ substr($order->order_comment, 0, 50) }}...
                        @endif
                    </td>
                @endif
                <td style="min-width:150px">{{ $order->status }} <br>
                    <i class="fas fa-car"></i>{{ $order->driver_status }}
                    @can('cash-collection-edit')
                        @if ($order->cashCollection)
                            <br><br>
                            <a href="{{ route('cashCollection.index') }}?order_id={{ $order->id }}">
                                <i class="fas fa-money-bill"></i> {{ $order->cashCollection->status }}
                            </a>
                        @endif
                    @endcan
                    @if ($order->status == 'Complete')
                        <br><br>
                        @if (!auth()->user()->hasRole('Supervisor'))
                            @if (!$order->cashCollection)
                                <a href="{{ route('cashCollection.create', $order->id) }}">
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
                                        <a class="dropdown-item"
                                            href="{{ route('orders.edit', $order->id) }}?edit=booking">Booking Edit</a>
                                    @endcan
                                    @can('order-status-edit')
                                        @if (auth()->user()->hasRole('Supervisor') && $order->status == 'Pending')
                                            <a class="dropdown-item"
                                                href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                                        @elseif(!auth()->user()->hasRole('Supervisor'))
                                            <a class="dropdown-item"
                                                href="{{ route('orders.edit', $order->id) }}?edit=status">Status Edit</a>
                                        @endif
                                    @endcan
                                    @can('order-detail-edit')
                                        <a class="dropdown-item"
                                            href="{{ route('orders.edit', $order->id) }}?edit=address">Address Edit</a>
                                    @endcan
                                    @can('order-affiliate-edit')
                                        <a class="dropdown-item"
                                            href="{{ route('orders.edit', $order->id) }}?edit=affiliate">Affiliate Edit</a>
                                    @endcan
                                    @can('order-comment-edit')
                                        <a class="dropdown-item"
                                            href="{{ route('orders.edit', $order->id) }}?edit=comment">Comment Edit</a>
                                    @endcan
                                    @can('order-driver-status-edit')
                                        <a class="dropdown-item"
                                            href="{{ route('orders.edit', $order->id) }}?edit=driver">Driver Edit</a>
                                    @endcan
                                    @can('order-driver-status-edit')
                                        <a class="dropdown-item"
                                            href="{{ route('orders.edit', $order->id) }}?edit=order_driver_status">Order Driver
                                            Status Edit</a>
                                    @endcan
                                    <a class="dropdown-item"
                                        href="{{ route('orders.edit', $order->id) }}?edit=custom_location">Add Custom
                                        Location</a>
                                    @can('order-chat')
                                        <a class="dropdown-item" href="{{ route('orders.chat', $order->id) }}">Chat</a>
                                    @endcan
                                    <a class="dropdown-item"
                                        href="{{ route('orders.edit', $order->id) }}?edit=services">Edit Services</a>
                                </div>
                            </li>
                        </ul>
                    @endcan
                    <form id="deleteForm{{ $order->id }}" action="{{ route('orders.destroy', $order->id) }}"
                        method="POST">
                        @csrf
                        @method('DELETE')
                        @can('order-delete')
                            <button type="button" onclick="confirmDelete('{{ $order->id }}')" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>
                        @endcan
                    </form>
                    @if ($order->status !== 'Complete' && Auth::User()->hasRole('Staff'))
                        @if ($order->status == 'Confirm')
                            <a class="btn btn-sm btn-success"
                                href="{{ route('updateOrderStatus', $order->id) }}?status=Accepted">
                                <i class="fas fa-thumbs-up"></i>
                            </a>

                            <a class="btn btn-sm btn-danger"
                                href="{{ route('updateOrderStatus', $order->id) }}?status=Rejected">
                                <i class="fas fa-thumbs-down"></i>
                            </a>
                        @endif
                        @if ($order->status == 'Accepted')
                            <a class="btn btn-sm btn-success"
                                href="{{ route('updateOrderStatus', $order->id) }}?status=Complete"><i
                                    class="fas fa-check-circle"></i></a>
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
