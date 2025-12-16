@extends('site.layout.app')

@section('content')
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

<div class="bg-slate-50 min-h-screen py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8 space-y-4">
            @if ($errors->any())
            <div class="rounded-lg bg-red-50 p-4 border border-red-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Whoops! There were some problems.</h3>
                        <div class="mt-2 text-sm text-red-700">
                            <ul class="list-disc pl-5 space-y-1">
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if(Session::has('success'))
            <div class="rounded-lg bg-emerald-50 p-4 border border-emerald-200">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-emerald-400" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-emerald-800">{{ Session::get('success') }}</p>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="md:flex p-8 gap-8">
                <div class="w-full md:w-auto flex-shrink-0 flex justify-center md:justify-start">
                    <div class="relative group">
                        <div class="w-40 h-40 md:w-52 md:h-52 rounded-full p-1 border-2 border-slate-100 bg-white shadow-lg">
                            <img src="/staff-images/{{ $user->staff->image ?? '' }}" alt="{{ $user->name }}" class="w-full h-full rounded-full object-cover">
                        </div>
                        @if($averageRating)
                        <div class="absolute bottom-2 right-2 bg-white rounded-full px-3 py-1.5 shadow-md border border-slate-100 flex items-center gap-1">
                            <i class="fas fa-star text-amber-400 text-xs"></i>
                            <span class="text-sm font-bold text-slate-800">{{ number_format($averageRating,1) }}</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="w-full mt-6 md:mt-2">
                    <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4">
                        <div>
                            <h1 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $user->name }}</h1>
                            <p class="text-lg text-emerald-600 font-medium mt-1">{{ $user->subTitles->pluck('name')->implode(' / ') }}</p>

                            <div class="mt-4 flex flex-wrap items-center gap-3">
                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-blue-50 text-blue-700 text-sm font-medium border border-blue-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 8a6 6 0 11-11.3-3.3A7 7 0 1019 9v2a2 2 0 01-2 2h-1v3l-3-1-3 1v-3H7a2 2 0 01-2-2V9a7 7 0 00-1 3 6 6 0 0013 0 6 6 0 001-4z" clip-rule="evenodd" />
                                    </svg>
                                    Delivered: {{ count($user->staffOrders) }}
                                </div>
                                <div class="inline-flex items-center px-3 py-1 rounded-full bg-purple-50 text-purple-700 text-sm font-medium border border-purple-100">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1.5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                                    </svg>
                                    Joined {{ $user->created_at->format('M Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                            <a href="{{ route('contact.staff', ['id' => $user->id]) }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-sky-600 hover:bg-sky-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-2">
                                <i class="far fa-envelope mr-2"></i> Message
                            </a>
                            <a href="{{ route('services.byStaff', ['id' => $user->id]) }}" class="inline-flex justify-center items-center px-6 py-2.5 bg-white border border-slate-300 text-slate-700 text-sm font-semibold rounded-lg hover:bg-slate-50 shadow-sm transition-all hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-slate-500 focus:ring-offset-2">
                                View Services
                            </a>
                        </div>
                    </div>

                    <div class="mt-8 pt-8 border-t border-slate-100">
                        <div class="prose prose-slate prose-sm max-w-none text-slate-600 leading-relaxed">
                            @if($user->staff->about)
                                {!! $user->staff->about !!}
                            @else
                                <p class="text-slate-400 italic">No biography available yet.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($socialLinks)
        <div class="mt-6">
            <div class="flex flex-wrap justify-center sm:justify-start gap-4">
                @if($user->staff->facebook)
                    <a href="{{$user->staff->facebook}}" target="_blank" class="transform transition duration-300 hover:-translate-y-1 flex items-center justify-center w-12 h-12 rounded-full bg-[#1877F2] text-white shadow-sm hover:shadow-md" aria-label="Facebook"><i class="fab fa-facebook-f text-lg"></i></a>
                @endif
                @if($user->staff->snapchat)
                    <a href="{{$user->staff->snapchat}}" target="_blank" class="transform transition duration-300 hover:-translate-y-1 flex items-center justify-center w-12 h-12 rounded-full bg-[#FFFC00] text-slate-900 shadow-sm hover:shadow-md" aria-label="Snapchat"><i class="fab fa-snapchat text-lg"></i></a>
                @endif
                @if($user->staff->youtube)
                    <a href="{{$user->staff->youtube}}" target="_blank" class="transform transition duration-300 hover:-translate-y-1 flex items-center justify-center w-12 h-12 rounded-full bg-[#FF0000] text-white shadow-sm hover:shadow-md" aria-label="YouTube"><i class="fab fa-youtube text-lg"></i></a>
                @endif
                @if($user->staff->instagram)
                    <a href="{{$user->staff->instagram}}" target="_blank" class="transform transition duration-300 hover:-translate-y-1 flex items-center justify-center w-12 h-12 rounded-full bg-gradient-to-tr from-yellow-400 via-red-500 to-purple-500 text-white shadow-sm hover:shadow-md" aria-label="Instagram"><i class="fab fa-instagram text-lg"></i></a>
                @endif
                @if($user->staff->tiktok)
                    <a href="{{$user->staff->tiktok}}" target="_blank" class="transform transition duration-300 hover:-translate-y-1 flex items-center justify-center w-12 h-12 rounded-full bg-black text-white shadow-sm hover:shadow-md" aria-label="TikTok"><i class="fab fa-tiktok text-lg"></i></a>
                @endif
            </div>
        </div>
        @endif

        <div class="border-b border-slate-200 my-10"></div>

        @if(count($user->staffYoutubeVideo))
        <div class="mt-10">
            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="fab fa-youtube text-red-600"></i> Video Gallery
            </h3>
            <div class="bg-white rounded-2xl shadow-sm p-2 border border-slate-100">
                <div id="videoCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators !bottom-0">
                        @foreach($user->staffYoutubeVideo->chunk($videoCarousel_chunk) as $key => $chunk)
                        <li data-target="#videoCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }} !bg-slate-400 !w-2 !h-2 !rounded-full"></li>
                        @endforeach
                    </ol>

                    <div class="carousel-inner rounded-xl overflow-hidden pb-8"> @foreach($user->staffYoutubeVideo->chunk($videoCarousel_chunk) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-4">
                                @foreach($chunk as $staffYoutubeVideo)
                                <div class="w-full">
                                    <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden shadow-md ring-1 ring-slate-900/5">
                                        <iframe class="w-full h-full" src="https://www.youtube.com/embed/{{ $staffYoutubeVideo->youtube_video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <a class="carousel-control-prev w-10 md:w-16" href="#videoCarousel" role="button" data-slide="prev">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-800/80 group-focus:ring-2 group-focus:ring-white group-focus:outline-none">
                            <span class="carousel-control-prev-icon !w-4 !h-4" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </span>
                    </a>
                    <a class="carousel-control-next w-10 md:w-16" href="#videoCarousel" role="button" data-slide="next">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-800/80 group-focus:ring-2 group-focus:ring-white group-focus:outline-none">
                            <span class="carousel-control-next-icon !w-4 !h-4" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if(count($user->staffImages))
        <div class="mt-10">
            <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                <i class="far fa-images text-sky-600"></i> Portfolio
            </h3>
            <div class="bg-white rounded-2xl shadow-sm p-2 border border-slate-100">
                <div id="imageCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators !bottom-0">
                        @foreach($user->staffImages->chunk($imageCarousel_chunk) as $key => $chunk)
                        <li data-target="#imageCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }} !bg-slate-400 !w-2 !h-2 !rounded-full"></li>
                        @endforeach
                    </ol>

                    <div class="carousel-inner rounded-xl overflow-hidden pb-8">
                        @foreach($user->staffImages->chunk($imageCarousel_chunk) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                             <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4">
                                @foreach($chunk as $image)
                                <div class="w-full">
                                    <div class="rounded-xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-300 h-64">
                                        <img src="/staff-images/{{ $image->image }}" class="w-full h-full object-cover transform hover:scale-105 transition-transform duration-500">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <a class="carousel-control-prev w-10 md:w-16" href="#imageCarousel" role="button" data-slide="prev">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-800/80">
                            <span class="carousel-control-prev-icon !w-4 !h-4" aria-hidden="true"></span>
                            <span class="sr-only">Previous</span>
                        </span>
                    </a>
                    <a class="carousel-control-next w-10 md:w-16" href="#imageCarousel" role="button" data-slide="next">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-slate-800/50 hover:bg-slate-800/80">
                            <span class="carousel-control-next-icon !w-4 !h-4" aria-hidden="true"></span>
                            <span class="sr-only">Next</span>
                        </span>
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if(count($service_categories) > 0 || count($services) > 0)
        <div class="mt-12 bg-white rounded-2xl shadow-sm border border-slate-200 p-8">
            <h3 class="text-2xl font-bold text-slate-900 mb-6 border-l-4 border-sky-500 pl-4">My Services</h3>
            
            @if(count($service_categories) > 0 )
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mt-4" id="categories">
                    @foreach($service_categories as $category)
                        @include('site.categories.category_card', ['category' => $category])
                    @endforeach
                </div>
            @endif

            @if(count($services) > 0 )
                <div id="services" class="mt-8">
                    <div class="owl-carousel owl-carousel-services">
                        @foreach($services as $service)
                            <div class="item px-2 py-4"> @include('site.services.card')
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        @endif

        <div class="mt-16">
            <h2 class="text-center text-3xl font-bold text-slate-900">Client Reviews</h2>
            <p class="text-center text-slate-500 mt-2 mb-8">What people are saying about my work</p>
            
            <div class="bg-slate-100/50 rounded-2xl p-6 md:p-10 relative">
                <div id="reviewsCarousel" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators !bottom-[-20px]">
                        @foreach($reviews->chunk($reviewsCarousel_chunk) as $key => $chunk)
                        <li data-target="#reviewsCarousel" data-slide-to="{{ $key }}" class="{{ $loop->first ? 'active' : '' }} !bg-slate-400 !w-2 !h-2 !rounded-full"></li>
                        @endforeach
                    </ol>
                    
                    <div class="carousel-inner pb-8">
                        @foreach($reviews->chunk($reviewsCarousel_chunk) as $chunk)
                        <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($chunk as $review)
                                <div class="h-full">
                                    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-200 h-full flex flex-col relative group hover:-translate-y-1 transition-transform duration-300">
                                        <div class="absolute top-4 right-4 text-slate-100 group-hover:text-sky-50 transition-colors">
                                            <i class="fas fa-quote-right fa-2x"></i>
                                        </div>
                                        
                                        <div class="flex-grow">
                                            <h5 class="text-lg font-bold text-slate-800">{{ $review->user_name }}</h5>
                                            <p class="text-sm text-slate-600 mt-3 leading-relaxed italic">"{{ $review->content }}"</p>
                                        </div>
                                        
                                        <div class="mt-5 pt-4 border-t border-slate-50">
                                            <div class="flex items-center space-x-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $review->rating)
                                                        <i class="fas fa-star text-amber-400 text-sm"></i>
                                                    @else
                                                        <i class="far fa-star text-slate-300 text-sm"></i>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                    
                    <a class="carousel-control-prev w-8" href="#reviewsCarousel" role="button" data-slide="prev">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-300 text-slate-600 hover:bg-sky-600 hover:text-white transition-colors">
                            <i class="fas fa-chevron-left text-xs"></i>
                        </span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next w-8" href="#reviewsCarousel" role="button" data-slide="next">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-300 text-slate-600 hover:bg-sky-600 hover:text-white transition-colors">
                            <i class="fas fa-chevron-right text-xs"></i>
                        </span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-12 mb-16">
            <div class="inline-block bg-white px-6 py-3 rounded-full shadow-sm border border-slate-200 mb-6">
                @php
                    $rating = $averageRating;
                    $fullStars = floor($rating);
                    $halfStar = $rating - $fullStars >= 0.5;
                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                @endphp
                
                <span class="text-2xl font-bold text-slate-800 mr-2 align-middle">{{ number_format($averageRating, 1) }}</span>
                <span class="inline-flex items-center gap-1 align-middle">
                    @for ($i = 0; $i < $fullStars; $i++)
                        <i class="fas fa-star text-amber-400 text-lg"></i>
                    @endfor
                    @if ($halfStar)
                        <i class="fas fa-star-half-alt text-amber-400 text-lg"></i>
                    @endif
                    @for ($i = 0; $i < $emptyStars; $i++)
                        <i class="far fa-star text-slate-300 text-lg"></i>
                    @endfor
                </span>
            </div>

            @if(auth()->check() && $app_flag === false)
                <div class="max-w-3xl mx-auto">
                    <div class="text-center">
                        <button class="inline-flex items-center px-8 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-lg hover:shadow-xl transition-all transform hover:-translate-y-0.5" id="review">
                            <i class="far fa-edit mr-2"></i> Write a Review
                        </button>
                    </div>
                    <div class="mt-8 bg-white rounded-xl p-8 shadow-lg border border-slate-100" id="review-form" style="display: none;">
                        @include('site.reviews.create')
                    </div>
                </div>
            @endif
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
        $('#review-form').slideDown();
        $('html, body').animate({
            scrollTop: $('#review-form').offset().top - 100
        }, 1000);
    });
</script>
<script>
    $(document).ready(function(){
        $(".owl-carousel-services").owlCarousel({
            loop: false,
            margin: 24, // Increased margin for Tailwind feel
            nav: true,
            dots: false,
            autoplay: true,
            autoplayTimeout: 10000,
            items: 3,
            stagePadding: 5, // Prevents shadow clipping
            responsive: {
                0: {
                    items: 1
                },
                640: {
                    items: 2
                },
                1024: {
                    items: 3
                }
            },
            navText: [
                '<div class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md text-slate-600 hover:text-sky-600"><i class="fa fa-chevron-left"></i></div>',
                '<div class="w-8 h-8 flex items-center justify-center bg-white rounded-full shadow-md text-slate-600 hover:text-sky-600"><i class="fa fa-chevron-right"></i></div>'
            ]
        });
    });
</script>
@endsection