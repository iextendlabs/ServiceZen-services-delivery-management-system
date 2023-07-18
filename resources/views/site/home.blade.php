@extends('site.layout.app')
@section('content')
<style>
  .pac-container {
    background-color: #FFF;
    z-index: 20;
    position: fixed;
    display: inline-block;
    float: left;
  }

  .modal {
    z-index: 20;
  }

  .modal-backdrop {
    z-index: 10;
  }
</style>
<section class="jumbotron text-center">
  <div class="container">
    @if(isset($category))
    <h1 class="jumbotron-heading">{{$category->title}}</h1>
    <p class="lead text-muted">{{$category->description}}</p>
    @else
    <h1 class="jumbotron-heading">Best In the Town Saloon Services</h1>
    <p class="lead text-muted">Get Your Desired Saloon Beauty service at Your Door, easy to schedule and just few clicks away.</p>
    @endif
  </div>
</section>

<input type="hidden" name="session" id="session" @if(isset($address)) value="true" @else value="false" @endif>
<div class="modal fade" id="myModal">
  <div class="modal-dialog">
    <div class="modal-content">

      <!-- Popup Header -->
      <div class="modal-header">
        <h5 class="modal-title">Popup Title</h5>
        <button type="button" class="close" data-dismiss="modal">&times;</button>
      </div>

      <!-- Popup Body -->
      <div class="modal-body">
        <div class="input-group mb-3">
          <input type="hidden" name="city" id="city">
          <input type="hidden" name="area" id="area">
          <div class="input-group-prepend" onclick="$('#searchField').val('')">
            <span class="input-group-text">x</span>
          </div>
          <input type="text" class="form-control" id="searchField" placeholder="Search on map">
          <div class="input-group-append">
            <button class="btn btn-primary" id="setLocation" type="button">Search on map</button>
          </div>
        </div>

        <div id="mapContainer" style="height: 400px; margin-top: 20px; display:none"></div>
      </div>

      <!-- Popup Footer -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save Changes</button>
      </div>

    </div>
  </div>
</div>

<div class="album py-5 bg-light">
  <div class="container">
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
    <div class="row">
      @foreach ($services as $service)
      <div class="col-md-4 service-box">
        <div class="card mb-4 box-shadow">
          <p class="card-text text-center"><b>{{ $service->name }}</b></p>
          <a href="/serviceDetail/{{ $service->id }}">
            <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
          </a>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted">
                @if(isset($service->discount))<s>@endif
                  @currency($service->price)
                  @if(isset($service->discount))</s>@endif
                @if(isset($service->discount))
                <b class="discount"> @currency( $service->discount )</b>
                @endif
              </small>

              <small class="text-muted"><b><i class="fa fa-clock"> </i> {{ $service->duration }}</b></small>
            </div>

            <a href="/addToCart/{{ $service->id }}"><button type="button" class="btn btn-block btn-primary"> Add to Cart</button></a>


          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
<script src="{{ asset('js/site.js') }}"></script>
<script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAP_KEY') }}&libraries=places&callback=mapReady&type=address"></script>

@endsection