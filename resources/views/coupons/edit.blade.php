@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Update Coupon</h2>
        </div>
    </div>
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
    <form action="{{ route('coupons.update',$coupon->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <ul class="nav nav-tabs" id="myTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false">History</a>
            </li>
        </ul>
        <div class="tab-content" id="myTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name</strong>
                            <input type="text" name="name" value="{{ $coupon->name }}" class="form-control" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Code</strong>
                            <input type="text" name="code" value="{{ $coupon->code }}" class="form-control" placeholder="Code">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Type</strong>
                            <select name="type" class="form-control">
                                @if($coupon->type == "Percentage")
                                <option value="Percentage" selected>Percentage</option>
                                <option value="Fixed Amount">Fixed Amount</option>
                                @elseif($coupon->type == "Fixed Amount")
                                <option value="Percentage">Percentage</option>
                                <option value="Fixed Amount" selected>Fixed Amount</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Discount</strong>
                            <input type="text" name="discount" value="{{ $coupon->discount }}" class="form-control" placeholder="Discount">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Date Start</strong>
                            <input type="date" name="date_start" value="{{ $coupon->date_start }}" class="form-control" placeholder="Date Start">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Date End</strong>
                            <input type="date" name="date_end" value="{{ $coupon->date_end }}" class="form-control" placeholder="Date End">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Status</strong>
                            <select name="status" class="form-control">
                                @if($coupon->status == 1)
                                <option value="1" selected>Enable</option>
                                <option value="0">Disable</option>
                                @elseif($coupon->status == 0)
                                <option value="1">Enable</option>
                                <option value="0" selected>Disable</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-block btn-primary">Save</button>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <table class="table table-striped table-bordered history-table">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Date Added</th>
                                </tr>
                                @if(count($coupon->couponHistory))
                                @foreach ($coupon->couponHistory as $history)
                                <tr>
                                    <td>{{ $history->order_id }}</td>
                                    <td>{{ $history->customer_id }}</td>
                                    <td>{{ $history->discount_amount }}</td>
                                    <td>{{ $history->created_at }}</td>
                                </tr>
                                @endforeach
                                @else
                                <tr>
                                    <td colspan="4" class="text-center"> There is no History</td>
                                </tr>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection