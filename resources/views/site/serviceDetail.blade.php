@extends('site.layout.app')
<base href="/public">
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
          <a href="/addToCart/{{ $service->id }}"><button type="button" class="btn btn-block btn-primary">Add to Cart</button></a>
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
    <h2>Package</h2><br>
    <div class="row">
      @foreach($service->package as $package)
      <div class="col-md-md-4">
        <div class="card mb-4 box-shadow">
          <p class="card-text"><b>{{ $package->service->name }}</b></p>
          <img class="card-img-top" src="./service-images/{{ $package->service->image }}" alt="Card image cap">
          <div class="card-body">
            <p class="card-text">{{ $package->service->short_description }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted"><b>Duration:{{ $package->service->duration }}</b></small>
              <small class="text-muted"><b>Price:@currency( $package->service->price )</b></small>
            </div>
          </div>
        </div>
      </div>
      @endforeach
    </div>
    @endif
  </div>
</div>
@endsection