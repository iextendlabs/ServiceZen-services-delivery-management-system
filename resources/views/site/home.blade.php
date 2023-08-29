@extends('site.layout.app')
@section('content')
<section class="jumbotron text-center">
  <div class="container">
    @if(isset($category))
    <h1 class="jumbotron-heading">{{$category->title}}</h1>
    <p class="lead text-muted">{{$category->description}}</p>
    @else
    <h1 class="jumbotron-heading" style="font-family: 'Titillium Web', sans-serif;">Best In the Town Saloon Services</h1>
    <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few clicks away.</p>
    @endif
  </div>
</section>
<div class="text-center">
  @if(Session::has('error'))
  <span class="alert alert-danger" role="alert">
    <strong>{{ Session::get('error') }}</strong>
  </span>
  @endif
  @if(Session::has('success'))
  <span class="alert alert-success" role="alert">
    <strong>{{ Session::get('success') }}</strong>
  </span>
  @endif
  @if(Session::has('cart-success'))
  <div class="alert alert-success" role="alert">
    <span>You have added service to your <a href="cart">shopping cart!</a></span><br>
    <span><a href="bookingStep">Book Now!</a></span><br>
    <span>To add more service<a href="/"> Continue</a></span>
  </div>
  @endif

</div>
<div class="album py-5 bg-light">
  <div class="container">

    <div class="row">
      @foreach ($services as $service)
      <div class="col-md-4 service-box">
        <div class="card mb-4 box-shadow">
          <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
          <a href="/serviceDetail/{{ $service->id }}">
            <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
          </a>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted service-box-price">
                @if(isset($service->discount))<s>@endif
                  @currency($service->price)
                  @if(isset($service->discount))</s>@endif
                @if(isset($service->discount))
                <b class="discount"> @currency( $service->discount )</b>
                @endif
              </small>

              <small class="text-muted service-box-time"><i class="fa fa-clock"> </i> {{ $service->duration }}</small>
            </div>

            <a href="/addToCart/{{ $service->id }}"><button type="button" class="btn btn-block btn-primary"> Book Now</button></a>


          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="row">
      <div class="col-md-12 text-center">
        {!! $services->links() !!}
      </div>
    </div>
    @if(count($FAQs) )
    <h1 id="faqs">Frequently Asked Questions</h1>
    <div id="accordion">
      @foreach ($FAQs as $FAQ)
      <div class="card">
        <div class="card-header" id="heading{{ $FAQ->id }}">
          <h5 class="mb-0">
            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{ $FAQ->id }}" aria-expanded="true" aria-controls="collapse{{ $FAQ->id }}">
              {{ $FAQ->question }}
            </button>
          </h5>
        </div>
        <div id="collapse{{ $FAQ->id }}" class="collapse" aria-labelledby="heading{{ $FAQ->id }}" data-parent="#accordion">
          <div class="card-body">
            {{ $FAQ->answer }}
          </div>
        </div>
      </div>
      @endforeach
    </div>
    <div class="row">
      <div class="col-md-2 offset-md-5 mt-3">
        <a href="{{ route('siteFAQs.index') }}"><button type="button" class="btn btn-block btn-primary">More..</button></a>
      </div>

    </div>
    @endif
  </div>
</div>
@endsection