@extends('site.layout.app')
@section('content')
<style>
  .card-img-top{
    height: 250px;
  }
  .card-body .card-text{
    height: 96px;
    overflow: hidden;
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
    <div class="row">
      @foreach ($services as $service)
      <div class="col-md-4">
        <div class="card mb-4 box-shadow">
          <p class="card-text"><b>{{ $service->name }}</b></p>
          <a href="/serviceDetail/{{ $service->id }}">
            <img class="card-img-top" src="./service-images/{{ $service->image }}" alt="Card image cap">
          </a>
          <div class="card-body">
            <p class="card-text">{{ $service->short_description }}</p>
            <div class="d-flex justify-content-between align-items-center">
              <small class="text-muted"><b>
                @if(isset($service->discount))<s>@endif
                  Price: ${{ $service->price }}</b>
                @if(isset($service->discount))</s>@endif
              </small>
              <small class="text-muted"><b>Duration:{{ $service->duration }}</b></small>
              <div class="btn-group">
                <a href="/addToCart/{{ $service->id }}"><button type="button" class="btn btn-sm btn-outline-secondary"> Add to Cart</button></a>
              </div>
            </div>
            @if(isset($service->discount))
              <small class="text-muted float-end"><b>Discount Price: ${{ $service->discount }}</b></small>
            @endif  
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endsection