@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2> Show Affiliate</h2>
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
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
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Name:</strong>
                    {{ $affiliate->name }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Email:</strong>
                    {{ $affiliate->email }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Code:</strong>
                    {{ $affiliate->affiliate->code }}
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Commission:</strong>
                    {{ $affiliate->affiliate->commission }}%
                </div>
            </div>
            @if($affiliate->affiliate && $affiliate->affiliate->number)
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Phone Number:</strong>
                    {{$affiliate->affiliate->number}}
                </div>
            </div>
            @endif
            @if($affiliate->affiliate && $affiliate->affiliate->whatsapp)
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Whatsapp Number:</strong>
                    {{ $affiliate->affiliate->whatsapp }}
                </div>
            </div>
            @endif
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Fix Salary:</strong>
                    @currency($affiliate->affiliate->fix_salary,true) (Rs.{{ $pkrRateValue * $affiliate->affiliate->fix_salary }})
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Bonus of {{ now()->format('F') }}:</strong>
                    @currency($bonus,true) (Rs.{{ $pkrRateValue * $bonus }})
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Product Sales of {{ now()->format('F') }}:</strong>
                    @currency($product_sales,true) (Rs.{{ $pkrRateValue * $product_sales }})
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <strong>Roles:</strong>
                    @if (!empty($affiliate->getRoleNames()))
                        @foreach ($affiliate->getRoleNames() as $v)
                            <span class="badge rounded-pill bg-dark">{{ $v }}</span>
                        @endforeach
                    @endif
                </div>
            </div>
            <hr>
            @if(count($affiliate->affiliateCategories) != 0)
            <div class="col-md-12">
                <h3>Category base commission</h3>
                <table class="table table-striped table-bordered album bg-light">
                    <tr>
                        <th>Category</th>
                        <th>Commission</th>
                    </tr>
                    @foreach ($affiliate->affiliateCategories as $affiliateCategory)
                        <tr>
                            <td>{{ $affiliateCategory->category->title }}</td>
                            <td>{{ $affiliateCategory->commission }}%</td>
                        </tr>
                    @endforeach
                </table>
            </div>
            <hr>
            @endif
            <div class="col-md-12">
                <h3>Customer</h3>
                @if (count($affiliateUser) != 0)
                    <table class="table table-striped table-bordered album bg-light">
                        <tr>
                            <th>Sr#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Number</th>
                            <th>Whatsapp</th>
                            <th>Zone</th>
                        </tr>
                        @foreach ($affiliateUser as $user)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $user->customer->name ?? '' }}</td>
                                <td>{{ $user->customer->email ?? '' }}</td>
                                <td>{{ optional($user->customer->customerProfiles->first())->number }}</td>
                                <td>{{ optional($user->customer->customerProfiles->first())->whatsapp ?? '' }}</td>
                                <td>{{ $user->customer->customerProfiles->pluck('area')->filter()->implode(', ') ?? '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                    {!! $affiliateUser->links() !!}
                @else
                    <div class="text-center">
                        <h4>There are no Customer</h4>
                    </div>
                @endif
            </div>

            <hr>
            <div class="col-md-12">
                <h3>Transaction</h3>
                @if (count($transactions) != 0)
                    <a href="{{ url('/affiliate/exportTransaction', ['User' => $affiliate->id]) }}"
                        class="btn btn-primary">Export
                        CSV</a>
                    <p>Current balance is: <b>@currency($total_balance,true) (Rs.{{ $total_balance_in_pkr }})</b></p>
                    <table class="table table-striped table-bordered album bg-light">
                        <tr>
                            <th>Sr#</th>
                            <th>Date Added</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Amount</th>
                            <th>Action</th>
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
                                <td>@currency($transaction->amount,true) (Rs.{{ $transaction->formatted_amount }})</td>
                                <td>
                                    <form action="{{ route('transactions.destroy', $transaction->id) }}" method="POST">
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
                    </table>
                    {!! $transactions->links() !!}
                @else
                    <div class="text-center">
                        <h4>There are no transactions</h4>
                    </div>
                @endif
            </div>

            <hr>
            <div class="col-md-6">
                <h3>Add Transaction</h3>
                <p>Current balance is: <b>@currency($total_balance,true) (Rs.{{ $total_balance_in_pkr }})</b></p>
                <p>Current balance with salary is: <b>@currency($total_balance,true)+@currency($affiliate->affiliate->fix_salary,true)
                        Rs.({{ $total_balance_in_pkr . '+' . $pkrRateValue * $affiliate->affiliate->fix_salary }})</b></p>
                <form action="{{ route('transactions.store') }}" method="POST" id="pay-transactions">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $affiliate->id }}">
                    <input type="hidden" name="pay" value="1">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Amount:</strong>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">{{ config('app.currency') }}</span>
                                    </div>
                                    <input type="number" name="amount" class="form-control" value="{{ old('amount') }}"
                                        placeholder="Amount">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Type:</strong>
                                <select name="type" class="form-control">
                                    <option value="Credit">Credit</option>
                                    <option value="Debit">Debit</option>
                                    <option value="Product Sale">Product Sale</option>
                                    <option value="Bonus">Bonus</option>
                                    <option value="Pay Salary">Pay Salary</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <span style="color: red;">*</span><strong>Description:</strong>
                                <textarea name="description" cols="10" rows="5" class="form-control">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-12 text-center">
                            <button type="submit" value="transaction" name="submit_type" class="btn btn-primary">Add
                                Transaction</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
