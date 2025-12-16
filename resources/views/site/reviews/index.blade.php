@extends('site.layout.app')
@section('content')
<div class="max-w-4xl mx-auto px-4 py-12">
    <header class="text-center mb-10">
        <h2 class="text-3xl font-extrabold text-gray-900">Customer Reviews</h2>
        <p class="mt-2 text-sm text-gray-500">Real feedback from people who used our services</p>
    </header>

    <section class="space-y-6">
        @if(isset($reviews) && $reviews->count())
            @foreach($reviews as $review)
            <article class="relative bg-white rounded-2xl shadow-lg overflow-hidden group transition-transform transform hover:-translate-y-1">
                {{-- left accent stripe --}}
                <div class="absolute left-0 top-0 h-full w-1 bg-gradient-to-b from-indigo-500 to-pink-500"></div>

                <div class="flex items-start gap-4 p-6">
                    <div class="flex-shrink-0">
                        <div class="h-14 w-14 rounded-full bg-gradient-to-br from-indigo-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg shadow-md">
                            {{ strtoupper(substr($review->user_name, 0, 1)) }}
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-4">
                            <div class="truncate">
                                <p class="text-lg font-semibold text-gray-900 truncate">{{ $review->user_name }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ \Illuminate\Support\Carbon::parse($review->created_at)->diffForHumans() }}</p>
                            </div>

                            <div class="flex items-center space-x-2">
                                <div class="flex items-center -ml-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rating)
                                            <svg class="w-4 h-4 text-yellow-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.95a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.95c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.448c-.784.57-1.84-.197-1.54-1.118l1.287-3.95a1 1 0 00-.364-1.118L2.645 9.377c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.95z"/></svg>
                                        @else
                                            <svg class="w-4 h-4 text-gray-200" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.95a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.37 2.448a1 1 0 00-.364 1.118l1.287 3.95c.3.921-.755 1.688-1.54 1.118L10 13.347l-3.37 2.448c-.784.57-1.84-.197-1.54-1.118l1.287-3.95a1 1 0 00-.364-1.118L2.645 9.377c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.95z"/></svg>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded">{{ $review->rating }} / 5</span>
                            </div>
                        </div>

                        <div class="mt-3 text-gray-700 text-sm leading-relaxed">
                            {{ $review->content }}
                        </div>

                        @if(!empty($review->images) && count((array)$review->images))
                        <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                            @foreach($review->images as $image)
                                <div class="overflow-hidden rounded-lg bg-gray-50">
                                    <img src="/review-images/{{ $image->image }}" alt="review image" class="w-full h-32 md:h-36 object-cover transform transition-transform duration-300 group-hover:scale-105">
                                </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </article>
            @endforeach
        @else
            <div class="bg-white shadow rounded-lg p-8 text-center">
                <p class="text-gray-600">No reviews yet. Be the first to leave a review!</p>
            </div>
        @endif
    </section>

    <div class="mt-8 flex justify-center">
        <div class="inline-flex bg-white rounded-md shadow-sm p-2">
            {!! $reviews->links() !!}
        </div>
    </div>

</div>
@endsection