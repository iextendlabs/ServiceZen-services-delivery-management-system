@extends('site.layout.app')
<style>
    .table {
        margin-bottom: 0px !important;
    }
</style>
<base href="/public">
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 py-5 text-center">
            <h2>Order</h2>
        </div>
    </div>
    @php
    $sub_total = 0;
    $staff_charges = 0;
    $total_amount = 0;
    $staff_transport_charges = 0;
    @endphp
    <div class="row">
        <div class="col-md-12">
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
            @if(Session::has('success'))
            <span class="alert alert-success" role="alert">
                <strong>{{ Session::get('success') }}</strong>
            </span>
            @endif

            <div class="float-right mb-2">
                <a class="btn btn-primary float-end no-print" href="{{ route('order.edit',$order->id) }}">Edit</a>
                <button class="btn btn-primary float-end no-print" id="review">Write a review</button>

                <button type="button" class="btn btn-danger float-end no-print" onclick="printDiv()"><i class="fa fa-print"></i> Download PDF</button>
            </div>
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left" colspan="2"><i class="fas fa-shopping-cart"></i> Order Details</td>
                <tr>
                    <td>
                        <b>Order ID:</b> #{{ $order->id }} <br><br>
                        <b>Date Added:</b> {{ $order->created_at }} <br><br>
                        <b>Order Status:</b> {{ $order->status }}
                    </td>
                    <td>
                        <b>Total Amount:</b> @currency( $order->total_amount ) <br><br>
                        <b>Payment Method:</b> {{ $order->payment_method }}
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left" colspan="3"><i class="fas fa-clock"></i> Appointment Details</td>
                <tr>
                    <td>
                        <b>Staff:</b> {{ $order->staff_name }}

                    </td>
                    <td>
                        <b>Date:</b> {{ $order->date }}
                    </td>
                    <td>
                        <b>Time:</b> {{ $order->time_slot_value }}
                    </td>
                </tr>
            </table>
            <table class="table table-striped table-bordered album bg-light">
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
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left" colspan="2">Customer Details</td>
                <tr>
                    <td>
                        <b>Name:</b> {{ $order->customer_name }} <br><br>
                        <b>Email:</b> {{ $order->customer_email }} <br><br>
                        <b>Gender:</b> {{ $order->gender }}
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
            <table class="table table-striped table-bordered album bg-light">
                <td class="text-left" colspan="4"><i class="fas fa-spa"></i> Services Details</td>
                <tr>
                    <th>Service Name</th>
                    <th>Status</th>
                    <th>Duration</th>
                    <th class="text-right">Amount</th>
                </tr>
                @foreach($order->orderServices as $orderService)
                <tr>
                    <td>{{ $orderService->service_name }}</td>
                    <td>{{ $orderService->status }}</td>
                    <td>{{ $orderService->duration ?? $orderService->service->duration ?? '' }}</td>
                    <td class="text-right">@currency($orderService->price)</td>
                </tr>
                @endforeach
                
                <tr>
                    <td colspan="3" class="text-right"><strong>Sub Total:</strong></td>
                    <td class="text-right">@currency($order->order_total->sub_total)</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Coupon Discount:</strong></td>
                    <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->discount ? '-'.$order->order_total->discount : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Staff Transport Charges:</strong></td>
                    <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->transport_charges ? $order->order_total->transport_charges : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Staff Charges:</strong></td>
                    <td class="text-right">{{ config('app.currency') }}{{ $order->order_total->staff_charges ? $order->order_total->staff_charges : 0 }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="text-right"><strong>Total:</strong></td>
                    <td class="text-right">@currency($order->total_amount)</td>
                </tr>
            </table>
            @if($order->order_comment)
            <table class="table table-striped table-bordered album bg-light">
                <th class="text-left" colspan="4">Order Comment</th>
                <tr>
                    <td class="text-left">{!! nl2br($order->order_comment) !!}</td>
                </tr>
            </table>
            @endif
        </div>
        <div class="col-md-6" id="review-form" style="display: none;">
        <h3>Write a review for staff</h3>
        <form action="{{ route('reviews.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
          @if($order->service_staff_id)
          <input type="hidden" name="user_id" value="{{ $order->service_staff_id }}">
          @endif
          <input type="hidden" name="store" value="1">
          <div class="row">
          <div class="col-md-12">
                  <div class="form-group">
                      <span style="color: red;">*</span><strong>Your Name:</strong>
                      <input type="text" name="user_name" value="{{old('content')}}" class="form-control">
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                      <span style="color: red;">*</span><strong>Review:</strong>
                      <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{old('content')}}</textarea>
                  </div>
              </div>
              <div class="col-md-12">
                  <div class="form-group">
                      <span style="color: red;">*</span><label for="rating">Rating</label><br>
                      @for($i = 1; $i <= 5; $i++) 
                      <div class="form-check form-check-inline">
                          <input class="form-check-input" type="radio" name="rating" id="rating{{ $i }}" value="{{ $i }}" {{ old('rating') == $i ? 'checked' : '' }}>
                          <label class="form-check-label" for="rating{{ $i }}">{{ $i }}</label>
                      </div>
                      @endfor
                  </div>
              </div>

          <div class="col-md-12 text-right">
              <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </div>
      </form>
      </div>
    </div>
</div>
<script>
    function printDiv() {
        window.print();
    }
</script>

<script>
  $(document).on('click','#review',function(){
    $('#review-form').show();
    $('html, body').animate({
      scrollTop: $('#review-form').offset().top
    }, 1000);
  });
</script>
@endsection