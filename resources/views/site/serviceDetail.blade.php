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
<div id="serviceDetailContainer" class="album py-5 bg-light">
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
          @if(count($service->addONs))
          <button class="btn btn-block btn-secondary" id="scroll">Add ONs</button>
          @endif
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
          <p class="card-text">{!! $service->short_description !!}</p>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <h3 class="text-center mt-3" style="font-family: 'Titillium Web', sans-serif;
    font-weight: bold;">Description</h3>
        {!! $service->description !!}
      </div>
    </div>
    <hr>
  </div>
</div>

<div class="album py-5 bg-light">
  <div class="container">
    @if(count($service->package))
    <h2>Package Services</h2><br>
    <div class="row">
      @foreach($service->package as $package)
      <div class="col-md-4 service-box">
        <div class="card mb-4 box-shadow">
          <p class="card-text service-box-title text-center"><b>{{ $package->name }}</b></p>
          <a href="/serviceDetail/{{ $package->service->id }}">
            <img class="card-img-top" src="./service-images/{{ $package->service->image }}" alt="Card image cap">
          </a>
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
    <hr>
    @if(count($service->addONs))
    <h2>Add ONs</h2><br>
    <div id="myCarousel" class="carousel slide" data-ride="carousel">
      <!-- Indicators -->
      <ol class="carousel-indicators">
        @foreach($service->addONs->chunk(6) as $key => $addONsChunk)
        <li data-target="#myCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
        @endforeach
      </ol>

      <!-- Slides -->
      <div class="carousel-inner">
        @foreach($service->addONs->chunk(6) as $key => $addONsChunk)
        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
          <div class="row">
            @foreach($addONsChunk as $addON)

            <div class="col-md-2 service-box">
              <div class="card mb-2 box-shadow">
                <a href="/serviceDetail/{{ $addON->service->id }}">
                  <div class="position-relative">
                    <img src="./service-images/{{ $addON->service->image }}" class="d-block carousel-image" alt="Image {{ $key }}">
                    <p class="card-text text-center service-name">{{ $addON->service->name }}</p>
                  </div>
                </a>
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <small class="text-mutede">
                      @if(isset($addON->service->discount))<s>@endif
                        @currency($addON->service->price)
                        @if(isset($addON->service->discount))</s>@endif
                      @if(isset($addON->service->discount))
                      <b class="discount"> @currency( $addON->service->discount )</b>
                      @endif
                    </small>
                    <small class="text-muted"><i class="fa fa-clock"> </i> {{ $addON->service->duration }}</small>
                  </div>
                  <a href="/addToCart/{{ $service->id }}" class="btn btn-sm btn-block btn-primary float-right mt-2"><i class="fa fa-plus"></i></a>
                </div>
              </div>
            </div>


            @endforeach
          </div>
        </div>
        @endforeach
      </div>

      <!-- Controls -->
      <a class="carousel-control-prev" href="#myCarousel" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#myCarousel" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
    @endif
    <hr>
    @if(count($FAQs))
    <h1>Frequently Asked Questions</h1>
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
    @endif
  </div>
</div>

<!-- @if(count($service->addONs))
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
    @endif -->

<script>
  $('#scroll').click(() => {
    $('html, body').animate({
      scrollTop: $('#add-ons').offset().top
    }, 1000);
  });
</script>
@endsection