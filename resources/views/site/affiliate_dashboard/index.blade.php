@extends('site.layout.app')

@section('content')
    <div class="container">
        <div class="row mt-3">
            <div class="col-12">
                @if ($message = Session::get('success'))
                    <div class="alert alert-success">
                        <span>{{ $message }}</span>
                    </div>
                @endif
                @if ($message = Session::get('error'))
                    <div class="alert alert-danger">
                        <span>{{ $message }}</span>
                    </div>
                @endif
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
            </div>
        </div>
        <div class="row bg-light py-5 mb-4">
            <div class="col-md-6 d-flex align-items-center">
                <h2>Dashboard</h2>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                @if (auth()->user()->hasRole('Staff'))
                    <a class="btn btn-success" href="/admin">Staff Dashborad</a>
                @endif
            </div>
            <div class="col-md-4 py-2">
                <div class="card">
                    <div class="card-header">Salary</div>
                    <div class="card-body analytic">
                        <i class="fa fa-credit-card"></i>
                        <span class="float-end">Rs.{{ $user->affiliate->fix_salary * $pkrRateValue ?? null }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="card">
                    <div class="card-header">Total Balance</div>
                    <div class="card-body analytic">
                        <i class="fa fa-credit-card"></i>
                        <span class="float-end">Rs.{{ $total_balance }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="card">
                    <div class="card-header">Product Sale of {{ now()->format('F') }}</div>
                    <div class="card-body analytic">
                        <i class="fa fa-pkr-sign"></i>
                        <span class="float-end"><b>Rs.</b>{{ $product_sales }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="card">
                    <div class="card-header">Total Bonus of {{ now()->format('F') }}</div>
                    <div class="card-body analytic">
                        <i class="fa fa-pkr-sign"></i>
                        <span class="float-end"><b>Rs.</b>{{ $bonus }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="card">
                    <div class="card-header">Total Order Commission of {{ now()->format('F') }}</div>
                    <div class="card-body analytic">
                        <i class="fa fa-pkr-sign"></i>
                        <span class="float-end"><b>Rs.</b>{{ $order_commission }}</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 py-2">
                <div class="card">
                    <div class="card-header">Other Income of {{ now()->format('F') }}</div>
                    <div class="card-body analytic">
                        <i class="fa fa-pkr-sign"></i>
                        <span class="float-end"><b>Rs.</b>{{ $other_income }}</span>
                    </div>
                </div>
            </div>
            @if ($user->affiliate->membershipPlan)
                <div class="col-md-4 py-2">
                    <div class="card">
                        <div class="card-header">Membership Plan</div>
                        <div class="card-body analytic">
                            <i class="fa fa-pkr-sign"></i>
                            <span class="float-end">{{ $user->affiliate->membershipPlan->plan_name }}
                                (Rs.{{ $user->affiliate->membershipPlan->membership_fee * $pkrRateValue }})</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        <div class="row bg-light py-3 mb-4">
            <div class="col-md-12">
                <h4>Account Actions</h4>
                <div class="d-flex justify-content-center">
                    <button class="btn btn-primary mx-2" onclick="showForm('withdrawForm')">Withdraw</button>
                    <button class="btn btn-secondary mx-2" onclick="showForm('transferForm')">Transfer</button>
                    <button class="btn btn-success mx-2" onclick="showForm('depositForm')">Deposit</button>
                </div>
            </div>
        </div>
        <div id="withdrawForm" class="action-form" style="display: none;">
            @if ($withdraws->where('status', 'Un Approved')->first())
                <div class="alert alert-danger">
                    <span>There is a pending request. You cannot make another withdrawal request until the current one is
                        completed.</span>
                </div>
            @else
                <form action="{{ route('affiliate.withdraw') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row bg-light py-3 mb-4">
                        <div class="col-md-12">
                            <h4>Withdraw Amount</h4><br>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Amount</strong>
                                <input type="number" name="amount" class="form-control" placeholder="Amount" required>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Payment as:</strong>
                                <select name="payment_method" class="form-control" required>
                                    <option></option>
                                    @foreach ($withdraw_payment_method as $payment_method)
                                        <option value="{{ $payment_method }}" 
                                            @if (old('payment_method') == $payment_method) selected @endif>{{ $payment_method }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Account Detail</strong>
                                <textarea name="account_detail" class="form-control" cols="20" rows="10" required>{{ old('account_detail') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Withdraw</button>
                        </div>
                    </div>
                </form>
            @endif
            <div class="row bg-light py-3 mb-4">
                <div class="col-md-12">
                    <h4>Withdraw Requests</h4><br>
                    <table class="table table-striped table-bordered album bg-light">
                        <tr>
                            <th>Sr#</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                        </tr>
                        @if (count($withdraws) != 0)
                            @foreach ($withdraws as $withdraw)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td>{{ $withdraw->amount * $pkrRateValue }}</td>
                                    <td>{{ $withdraw->status }}</td>
                                    <td>{{ $withdraw->payment_method }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">There are no Withdraw Request</td>
                            </tr>
                        @endif
                    </table>

                </div>
            </div>
        </div>

        <!-- Transfer Form -->
        <div id="transferForm" class="action-form" style="display: none;">
            <form action="{{ route('affiliate.transfer') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row bg-light py-3 mb-4">
                    <div class="col-md-12">
                        <h4>Transfer Amount</h4><br>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Amount</strong>
                            <input type="number" name="amount" class="form-control" placeholder="Amount" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Recipient Name</strong>
                            <input type="text" name="recipient_name" class="form-control"
                                placeholder="Recipient Name" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Recipient Code</strong>
                            <input type="text" name="recipient_code" class="form-control"
                                placeholder="Recipient Code" required>
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-secondary">Transfer</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Deposit Form -->
        <div id="depositForm" class="action-form" style="display: none;">
            <form action="{{ route('affiliate.deposit') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row bg-light py-3 mb-4">
                    <div class="col-md-12">
                        <h4>Deposit Amount</h4><br>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Amount</strong>
                            <input type="number" name="amount" class="form-control" placeholder="Amount" required>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Payment Method</strong>
                            <select name="payment_method" class="form-control">
                                <option value="Credit-Debit-Card"> Credit or Debit Card</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Deposit</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row bg-light py-3 mb-4">
            <div class="col-md-12">
                <a href="{{ route('affiliateUrl', ['affiliate_id' => auth()->user()->id]) }}">My Affiliate URL</a>
                <p>{{ route('affiliateUrl', ['affiliate_id' => auth()->user()->id]) }}</p>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Code:</strong>
                    <input disabled type="text" name="code" id="code" class="form-control"
                        placeholder="Code" value="{{ $user->affiliate->code ?? null }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Commission:</strong>
                    <input disabled type="text" name="commission" id="commission" class="form-control"
                        placeholder="Commission" value="{{ $user->affiliate->commission ?? null }}">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <strong>Fix Salary:</strong>
                    <input disabled type="text" name="fix_salary" id="fix_salary" class="form-control"
                        placeholder="Fix Salary"
                        value="{{ 'Rs.' . $user->affiliate->fix_salary * $pkrRateValue ?? null }}">
                </div>
            </div>
            <div class="col-md-12">
                <p>Your current balance is: <b>Rs.{{ $total_balance }}</b></p>
                @if (count($transactions) != 0)
                    <table class="table table-striped table-bordered album bg-light">
                        <tr>
                            <th>Sr#</th>
                            <th>Date Added</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Amount</th>
                        </tr>
                        @foreach ($transactions as $transaction)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $transaction->created_at }}</td>
                                <td>{{ $transaction->type }}</td>
                                <td>
                                    @if ($transaction->order_id)
                                        Order ID: #{{ $transaction->order_id }}
                                    @elseif($transaction->description)
                                        {{ $transaction->description }}
                                    @else
                                        Paid Amount
                                    @endif
                                </td>
                                <td>Rs.{{ $transaction->formatted_amount }}</td>
                            </tr>
                        @endforeach
                    </table>
                    {!! $transactions->links() !!}
                @else
                    <div class="text-center">
                        <h4>There are no transactions</h4>
                    </div>
                @endif
            </div>
        </div>
        <div class="row bg-light py-3 mb-4">
            <div class="col-md-12">
                <strong>My Customer</strong>
                <table class="table table-striped table-bordered album bg-light">
                    <td class="text-left" colspan="6"><i class="fas fa-filter"></i> Filter Customer by Order Created
                        Date
                    </td>
                    <tr>
                        <td colspan="6">
                            <form action="{{ route('affiliate_dashboard.index') }}" method="GET"
                                enctype="multipart/form-data">
                                <div class="row d-flex flex-wrap justify-content-md-center">
                                    <div class="col-md-6">
                                        <div class="col-md-12">
                                            <strong>Order Count:</strong>
                                            <input type="number" name="order_count" class="form-control"
                                                value="{{ $filter_order_count }}">
                                        </div>
                                        <div class="col-md-12">
                                            <strong>Date From:</strong>
                                            <input type="date" name="date_from" class="form-control"
                                                value="{{ $filter_date_from }}">
                                        </div>
                                        <div class="col-md-12">
                                            <strong>Date To:</strong>
                                            <input type="date" name="date_to" class="form-control"
                                                value="{{ $filter_date_to }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="row d-flex flex-wrap justify-content-md-center">
                                    <div class="col-md-6 mt-3">
                                        <div class="col-md-8 offset-md-4">
                                            <div class="d-flex flex-wrap justify-content-md-end">
                                                <div class="col-md-3">
                                                    <a href="{{ url()->current() }}" class="btn btn-secondary">Reset</a>
                                                </div>
                                                <div class="col-md-9">
                                                    <button type="submit"
                                                        class="btn btn-block btn-primary">Filter</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                        </td>
                    </tr>
                </table>
                <table class="table table-striped table-bordered album bg-light">
                    <tr>
                        <th>Sr#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Orders</th>
                        <th>Number</th>
                        <th>Whatsapp</th>
                        <th>Zone</th>
                    </tr>
                    @if (count($affiliateUser) != 0)
                        @foreach ($affiliateUser as $user)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $user->customer->name ?? '' }}</td>
                                <td>{{ $user->customer->email ?? '' }}</td>
                                <td>{{ $user->order_count }}</td>
                                <td>{{ optional($user->customer->customerProfiles->first())->number ?? '' }}</td>
                                <td>{{ optional($user->customer->customerProfiles->first())->whatsapp ?? '' }}</td>
                                <td>{{ $user->customer->customerProfiles->pluck('area')->filter()->implode(', ') ?? '' }}</td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="7" class="text-center">There are no Customer</td>
                        </tr>
                    @endif
                </table>
                @if (count($affiliateUser) != 0)
                    {!! $affiliateUser->links() !!}
                @endif

            </div>
        </div>
    </div>
    <script>
        function showForm(formId) {
            // Hide all forms
            document.querySelectorAll('.action-form').forEach(form => form.style.display = 'none');
            // Show the selected form
            document.getElementById(formId).style.display = 'block';
        }
    </script>
@endsection
