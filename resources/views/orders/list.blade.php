<table class="table table-bordered table-responsive">
                <tr>
                    <th>Order #</th>
                    <th>Staff</th>
                    <th><i class="fas fa-clock"></i> Appointment Date</th>
                    <th><i class="fas fa-clock"></i> Slots</th>

                    @if(auth()->user()->getRoleNames() == '["Supervisor"]')
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
                    <th style="min-width:160px">Action</th>
                </tr>
                @if(count($orders))
                @foreach ($orders as $order)
                <tr>
                    <th>#{{ $order->id }}</th>
                    <td>{{ $order->staff_name }}</td>
                    <td>{{ $order->date }}</td>
                    <td>{{ $order->time_slot_value }}</td>
                    @if(auth()->user()->getRoleNames() == '["Supervisor"]')
                    <td>{{ $order->landmark }}</td>
                    <td>{{ $order->area }}</td>
                    <td>{{ $order->city }}</td>
                    <td>{{ $order->buildingName }}</td>
                    @else
                    <td>{{ $order->customer_name }}</td>
                    <td>@currency($order->total_amount)</td>
                    <td>{{ $order->payment_method }}</td>
                    <td>{{ $order->order_comment }}</td>
                    @endif
                    <td>{{ $order->status }}</td>
                    <td>{{ $order->created_at }}</td>
                    <td>
                        <form action="{{ route('orders.destroy',$order->id) }}" method="POST">
                            <a class="btn btn-info" href="{{ route('orders.show',$order->id) }}">
                                <i class="fas fa-eye"></i>
                            </a>

                            @can('order-edit')
                            <a class="btn btn-primary" href="{{ route('orders.edit',$order->id) }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @csrf
                            @method('DELETE')
                            @can('order-delete')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i>
                            </button>

                            @endcan
                        </form>
                    </td>
                </tr>
                @endforeach
                @else
                <tr>
                    <td colspan="11" class="text-center"> There is no Order</td>
                </tr>
                @endif
            </table>