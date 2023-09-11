@extends('site.layout.app')
@section('content')
<style>
    .card-img-top {
        height: 300px !important;
        width: 300px;
    }
</style>
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
                <hr>
            </div>
            <div class="col-md-12 text-center">
                <img src="./staff-images/{{ $user->staff->image }}" alt="{{ $user->name }}" class="img-fluid rounded-circle mb-3 card-img-top">
                <hr>
            </div>

            <div class="col-md-10 offset-md-1">
                @if($user->staff->about)
                <h3 class="text-center">About</h3>
                {!! $user->staff->about !!}
                @endif

                <hr>
                <p class="text-center">
                    <strong>Delivered Order: {{ count($user->orders) }}</strong>
                </p>
            </div>
            <!-- Social Links -->
            @if($socialLinks)
            <div class="col-md-12 text-center">
                <h3>Social Links</h3>

                <!-- Facebook -->
                <a style="color: #3b5998;" href="{{$user->staff->facebook}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-facebook-f fa-lg"></i>
                </a>

                <!-- Twitter -->
                <a style="color: #fffc00;" href="{{$user->staff->snapchat}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-snapchat fa-lg"></i>
                </a>

                <!-- Youtube -->
                <a style="color: #ed302f;" href="{{$user->staff->youtube}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-youtube fa-lg"></i>
                </a>

                <!-- Instagram -->
                <a style="color: #ac2bac;" href="{{$user->staff->instagram}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-instagram fa-lg"></i>
                </a>

                <!-- Tiktok -->
                <a style="color: #1e3050;" href="{{$user->staff->tiktok}}" target="_blank" class="btn btn-lg" role="button">
                    <i class="fab fa-tiktok"></i>
                </a>
            </div>
            @endif
            <!-- Staff Gallery -->
            @if(count($user->staffYoutubeVideo))
            <div class="col-md-12 text-center mt-3 mb-3">
                @foreach($user->staffYoutubeVideo as $staffYoutubeVideo)
                <iframe width="592" height="333" src="https://www.youtube.com/embed/{{ $staffYoutubeVideo->youtube_video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                @endforeach
            </div>
            @endif
            @if(count($user->staffImages))
            <div class="col-md-12 mt-2 mb-3">
                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        @foreach($user->staffImages->chunk(3) as $key => $chunk)
                        <li data-target="#imageCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }}"></li>
                        @endforeach
                    </ol>
                    <div class="carousel-inner">
                        @foreach($user->staffImages->chunk(3) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="row">
                                @foreach($chunk as $image)
                                <div class="col-md-4">
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
            @foreach($categories as $category)
            @if($category->status == "1")
            @if(count($category->childCategories) == 0)
            @if(!$category->parentCategory)
            @include('site.categories.category_card', ['category' => $category])
            @endif
            @else
            @include('site.categories.category_card', ['category' => $category])
            @endif
            @endif
            @endforeach
        </div>
        <h3 class="text-center">Reviews</h3>
        <div class="row">
            @if($reviews)
            @foreach($reviews as $review)
            <div class="col-md-5 offset-md-4">
                <div class="card m-2">
                    <div class="card-body">
                        <h5 class="card-title">{{$review->user_name}}</h5>
                        <p class="card-text" style="height: 50px;">{{$review->content}}</p>
                        <div class="star-rating">
                            @for($i = 1; $i <= 5; $i++) @if($i <=$review->rating)
                                <span class="text-warning">&#9733;</span>
                                @else
                                <span class="text-muted">&#9734;</span>
                                @endif
                                @endfor
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @endif

            <div class="col-md-5 offset-md-4">

                @for($i = 1; $i <= 5; $i++) @if($i <=$averageRating) <span class="text-warning">&#9733;</span>
                    @else
                    <span class="text-muted">&#9734;</span>
                    @endif
                    @endfor
                    {{count($reviews)}} Reviews
                    @if(auth()->check())
                    <button class="btn btn-block btn-primary" id="review">Write a review</button>
                    @endif
                    <div id="review-form" style="display: none;">
                        @include('site.reviews.create')
                    </div>
            </div>
        </div>
    </div>
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