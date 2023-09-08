@extends('site.layout.app')
@section('content')
<style>
  #staffCarousel img {
    height: 200px !important;
    width: 200px;
  }
</style>
<div class="container">
  <div class="text-center mt-3">
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
  @if($slider_images->value && !isset($category))
  <div class="row">
    <div id="imageSlider" class="carousel slide mt-3" data-ride="carousel">
      <ol class="carousel-indicators">
        @foreach (explode(',', $slider_images->value) as $index => $imagePath)
        <li data-target="#imageSlider" data-slide-to="{{ $index }}" @if($index===0) class="active" @endif></li>
        @endforeach
      </ol>
      <div class="carousel-inner">
        @foreach (explode(',', $slider_images->value) as $index => $imagePath)
        <div class="carousel-item @if($index === 0) active @endif">
          <img src="/slider-images/{{ $imagePath }}" alt="Slide {{ $index + 1 }}" class="d-block w-100">
          <!-- <div class="carousel-caption d-none d-md-block">
            <h5>Slide {{ $index + 1 }} label</h5>
            <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur.</p>
          </div> -->
        </div>
        @endforeach
      </div>
      <a class="carousel-control-prev" href="#imageSlider" role="button" data-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="sr-only">Previous</span>
      </a>
      <a class="carousel-control-next" href="#imageSlider" role="button" data-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="sr-only">Next</span>
      </a>
    </div>
  </div>
  @endif
</div>
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
<div class="container">
  <div class="row" id="categories">
    @if(isset($all_categories))
      @foreach($all_categories as $single_category)
        @if(count($single_category->childCategories) == 0)
          @if(!$single_category->parentCategory)
            @include('site.categories.category_card', ['category' => $single_category])
          @endif
        @else
          @include('site.categories.category_card', ['category' => $single_category])
        @endif
      @endforeach
    @endif
  </div>

  <div class="row" id="categories">
    @if(isset($category))
      @if(count($category->childCategories))
        @foreach($category->childCategories as $childCategory)
          @include('site.categories.category_card', ['category' => $childCategory])
        @endforeach
      @endif
    @endif
  </div>
</div>

<hr>
<div class="album py-5 bg-light">
  <div class="container">

    <div class="row">
      @foreach ($services as $service)
      <div class="col-md-4 service-box">
        <div class="card mb-4 box-shadow">
          <a href="/serviceDetail/{{ $service->id }}">
            <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
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
    <div class="row">
      <div class="col-md-12">
        <h2 class="text-center">Customer Reviews</h2>
        <div id="reviewsCarousel" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            @foreach($reviews->chunk(3) as $key => $chunk)
            <li data-target="#reviewsCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
            @endforeach
          </ol>
          <div class="carousel-inner">
            @foreach($reviews->chunk(3) as $chunk)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
              <div class="row">
                @foreach($chunk as $review)
                <div class="col-md-4">
                  <div class="card mb-4 text-center">
                    <div class="card-body" style="height: 215px !important">
                      <h5 class="card-title">{{ $review->user_name }}</h5>
                      <p class="card-text">{{ substr($review->content, 0, $review_char_limit) }}...</p>
                      <p class="card-text">
                        @for($i = 1; $i <= 5; $i++) @if($i <=$review->rating)
                          <span class="text-warning">&#9733;</span>
                          @else
                          <span class="text-muted">&#9734;</span>
                          @endif
                          @endfor
                      </p>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endforeach
          </div>
          <a class="carousel-control-prev" href="#reviewsCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#reviewsCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
      </div>
      <div class="col-md-12 text-center mb-2">
        <a class="btn btn-primary" href="{{ route('siteReviews.index') }}">All Reviews</a>
      </div>
      @if(auth()->check())
      <div class="col-md-12 text-center">
        <button class="btn btn-primary" id="review">Write a Review</button>
      </div>
      <div class="col-md-6" id="review-form" style="display: none;">
        @include('site.reviews.create')
      </div>
      @endif
      <div class="col-md-12">
        <h2 class="text-center">Our Team</h2>

        <div id="staffCarousel" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            @foreach($staffs->chunk(4) as $key => $chunk)
            <li data-target="#staffCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
            @endforeach
          </ol>
          <div class="carousel-inner">
            @foreach($staffs->chunk(4) as $chunk)
            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
              <div class="row">
                @foreach($chunk as $staff)
                <div class="col-md-3">
                  <div class="card mb-3">
                    <div class="col-md-12 text-center">
                      <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
                        <img src="./staff-images/{{ $staff->staff->image }}" class="card-img-top img-fluid rounded-circle" alt="{{ $staff->name }}">
                      </div>
                    </div>
                    <div class="card-body text-center">
                      <h5 class="card-title">{{ $staff->name }}</h5>
                      <a href="{{ route('staffProfile.show',$staff->id) }}" class="btn btn-block btn-primary">View</a>
                    </div>
                  </div>
                </div>
                @endforeach
              </div>
            </div>
            @endforeach
          </div>
          <a class="carousel-control-prev" href="#staffCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
          </a>
          <a class="carousel-control-next" href="#staffCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
          </a>
        </div>
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
<script>
  $(document).on('click', '#review', function() {
    $('#review-form').show();
    $('html, body').animate({
      scrollTop: $('#review-form').offset().top
    }, 1000);
  });
</script>
@endsection