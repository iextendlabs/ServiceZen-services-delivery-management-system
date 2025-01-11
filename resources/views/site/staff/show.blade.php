@extends('site.layout.app')
@section('content')
<style>
    .card-img-top {
        height: 300px !important;
        width: 300px;
    }
</style>
@php
if($app_flag === true){
$videoCarousel_chunk = 1;
$imageCarousel_chunk = 1;
$reviewsCarousel_chunk = 1;
}else{
$videoCarousel_chunk = 2;
$imageCarousel_chunk = 3;
$reviewsCarousel_chunk = 3;
}

@endphp
<div class="album py-5 bg-light">
    <div class="container">
        <div class="row">
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
        </div>
        <div class="row">
            <div class="col-md-12 text-center">
                <h2>{{ $user->name }}</h2>
                <h2>{{ $user->staff->sub_title ?? "" }}</h2>
                <hr>
            </div>
            <div class="col-md-4 text-center">
                <img src="./staff-images/{{ $user->staff->image ?? "" }}" alt="{{ $user->name }}" class="img-fluid rounded-circle mb-3 card-img-top">
            </div>

            <div class="col-md-8">
                @if($user->staff->about)
                {!! $user->staff->about !!}
                @endif
            </div>
            <div class="col-md-12 text-center">
                <hr>
                <h5>
                    <strong>Delivered Order: {{ count($user->staffOrders) }}</strong>
                </h5>
            </div>
            <!-- Social Links -->
            @if($socialLinks)
            <div class="col-md-12 text-center">
                <h3>Social Links</h3>

                <!-- Facebook -->
                @if($user->staff->facebook)
                <a style="color: #3b5998;" href="{{$user->staff->facebook}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-facebook-f fa-lg"></i>
                </a>
                @endif

                <!-- Twitter -->
                @if($user->staff->snapchat)
                <a style="color: #fffc00;" href="{{$user->staff->snapchat}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-snapchat fa-lg"></i>
                </a>
                @endif
                <!-- Youtube -->
                @if($user->staff->youtube)
                <a style="color: #ed302f;" href="{{$user->staff->youtube}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-youtube fa-lg"></i>
                </a>
                @endif
                <!-- Instagram -->
                @if($user->staff->instagram)
                <a style="color: #ac2bac;" href="{{$user->staff->instagram}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>
                @endif
                <!-- Tiktok -->
                @if($user->staff->tiktok)
                <a style="color: #1e3050;" href="{{$user->staff->tiktok}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-tiktok"></i>
                </a>
                @endif
            </div>
            @endif
            <hr>
            <!-- Staff Gallery -->
            @if(count($user->staffYoutubeVideo))

            <div class="col-md-12 mt-2 mb-3">
                <div id="videoCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach($user->staffYoutubeVideo->chunk($videoCarousel_chunk) as $key => $chunk)
                        <li data-target="#videoCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach($user->staffYoutubeVideo->chunk($videoCarousel_chunk) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $staffYoutubeVideo)
                                <div class="col-md-6 col-xs-12">
                                    <div class="embed-responsive embed-responsive-16by9 ">
                                        <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/{{ $staffYoutubeVideo->youtube_video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#videoCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#videoCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

            </div>


            @endif
            @if(count($user->staffImages))
            <div class="col-md-12 mt-2 mb-3">
                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach($user->staffImages->chunk($imageCarousel_chunk) as $key => $chunk)
                        <li data-target="#imageCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach($user->staffImages->chunk($imageCarousel_chunk) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $image)
                                <div class="col-md-4 col-xs-12">
                                    <img src="/staff-images/{{ $image->image }}" class="d-block w-100 card-img-top">
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <a class="carousel-control-prev" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#imageCarousel" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

            </div>
            @endif
        </div>
        <h3 class="text-center">My Services</h3>
        <div class="row" id="categories">
            @foreach($service_categories as $category)
            @include('site.categories.category_card', ['category' => $category])
            @endforeach
        </div>
        <div class="col-md-12">
            <h2 class="text-center">Customer Reviews</h2>
            <div id="reviewsCarousel" class="carousel slide" data-ride="carousel">
                <ol class="carousel-indicators">
                    @foreach($reviews->chunk($reviewsCarousel_chunk) as $key => $chunk)
                    <li data-target="#reviewsCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                    @endforeach
                </ol>
                <div class="carousel-inner">
                    @foreach($reviews->chunk($reviewsCarousel_chunk) as $chunk)
                    <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                        <div class="row">
                            @foreach($chunk as $review)
                            <div class="col-md-4 col-xs-12">
                                <div class="card mb-4 text-center">
                                    <div class="card-body" style="height: 215px !important">
                                        <h5 class="card-title">{{ $review->user_name }}</h5>
                                        <p class="card-text">{{ $review->content }}</p>
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
            @php
                $rating = $averageRating;
                $fullStars = floor($rating);
                $halfStar = $rating - $fullStars >= 0.5;
                $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
            @endphp

            @for ($i = 0; $i < $fullStars; $i++)
                <i class="fas fa-star text-warning fa-xs"></i>
            @endfor

            @if ($halfStar)
                <i class="fas fa-star-half-alt text-warning fa-xs"></i>
            @endif

            @for ($i = 0; $i < $emptyStars; $i++)
                <i class="far fa-star text-muted fa-xs"></i>
            @endfor
        </div>
        @if(auth()->check() && $app_flag === false)
        <div class="col-md-12 text-center">
            <button class="btn btn-primary" id="review">Write a Review</button>
        </div>
        <div class="col-md-6" id="review-form" style="display: none;">
            @include('site.reviews.create')
        </div>
        @endif
    </div>
</div>
</div>
</div>
@if($app_flag === true)
<script>
    $(document).ready(function() {
        $("header, footer").hide();
        $("#categories a").attr("href", "javascript:void(0);");
    });
</script>
@endif
<script>
    $(document).on('click', '#review', function() {
        $('#review-form').show();
        $('html, body').animate({
            scrollTop: $('#review-form').offset().top
        }, 1000);
    });
</script>
@endsection