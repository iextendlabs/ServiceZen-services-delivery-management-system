@extends('site.layout.app')
<style>
  .box-shadow {
    background: none !important;
  }
</style>
@section('content')
<section class="jumbotron text-center">
  <div class="container">
    <h1 class="jumbotron-heading">Best In the Town Saloon Services</h1>
    <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few clicks away.</p>
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
    <span>To add more service<a href="/"> Continue</a></span>
  </div>
  @endif

</div>
<div class="album py-5 bg-light">
  <div class="container">

    <h1 class="card-text text-center service-title"><b>{{ $service->name }}</b></h1>

    <div class="row">

      <div class="col-md-8">
        <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
      </div>
      <div class="col-md-4 box-shadow">
        <div class="card-body">
          <p class="text-muted">
            @if(isset($service->discount))<s>@endif
              @currency($service->price)
              @if(isset($service->discount))</s>@endif
            @if(isset($service->discount))
            <b class="discount"> @currency( $service->discount )</b>
            @endif
          </p>

          <p class="text-muted"><b><i class="fa fa-clock"> </i> {{ $service->duration }}</b></p>
          <a href="/addToCart/{{ $service->id }}" class="btn btn-block btn-primary">Add to Cart</a>
          <button class="btn btn-block btn-secondary" id="scroll">Add ONs</button>
          <!-- AddToAny BEGIN -->
          <div class="a2a_kit a2a_kit_size_32 a2a_default_style service-social-icon">
            <a class="a2a_dd" href="https://www.addtoany.com/share"></a>
            <a class="a2a_button_facebook"></a>
            <a class="a2a_button_twitter"></a>
            <a class="a2a_button_whatsapp"></a>
            <a class="a2a_button_telegram"></a>
          </div>
          <script async src="https://static.addtoany.com/menu/page.js"></script>
          <!-- AddToAny END -->
          <p class="card-text">{!! $service->description !!}</p>
        </div>
      </div>
    </div>
    <hr>
    @if(count($service->package))
    <h2>Package Services</h2><br>
    <div class="row">
      @foreach($service->package as $package)
      <div class="col-md-4 service-box">
        <div class="card mb-4 box-shadow">
          <p class="card-text service-box-title text-center"><b>{{ $package->service->name }}</b></p>
          <img class="card-img-top" src="./service-images/{{ $package->service->image }}" alt="Card image cap">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted service-box-price">
                @if(isset($package->service->discount))<s>@endif
                  @currency($package->service->price)
                  @if(isset($package->service->discount))</s>@endif
                @if(isset($package->service->discount))
                <b class="discount"> @currency( $package->service->discount )</b>
                @endif
              </small>

              <small class="text-muted service-box-time"><i class="fa fa-clock"> </i> {{ $package->service->duration }}</small>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif

    @if(count($service->addONs))
    <h2>Add ONs</h2><br>
    <div id="add-ons" class="row">
      @foreach($service->addONs as $addONs)
      <div class="col-md-4 service-box">
        <div class="card mb-4 box-shadow">
          <p class="card-text service-box-title text-center"><b>{{ $addONs->service->name }}</b></p>
          <a href="/serviceDetail/{{ $addONs->service->id }}">
            <img class="card-img-top" src="./service-images/{{ $addONs->service->image }}" alt="Card image cap">
          </a>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted service-box-price">
                @if(isset($addONs->service->discount))<s>@endif
                  @currency($addONs->service->price)
                  @if(isset($addONs->service->discount))</s>@endif
                @if(isset($addONs->service->discount))
                <b class="discount"> @currency( $addONs->service->discount )</b>
                @endif
              </small>

              <small class="text-muted service-box-time"><i class="fa fa-clock"> </i> {{ $addONs->service->duration }}</small>
            </div>

            <a href="/addToCart/{{ $addONs->service->id }}"><button type="button" class="btn btn-block btn-primary"> Book Now</button></a>


          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</div>
<script>
  $('#scroll').click(()=>{
    $('html, body').animate({
      scrollTop: $('#add-ons').offset().top
    }, 1000);
  });
</script>
@endsection