<div class="card mb-3">
    <div class="col-md-12 text-center">
        <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
            @php
                $imagePath = 'staff-images/' . $staff->staff->image;
                $altText = $staff->staff->image_alt ?? $staff->name;
                $width = 250;
                $height = 250;
            @endphp

            <img class="card-img-top img-fluid rounded-circle"
                src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
                srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x,
                                 {{ url('img/' . $imagePath) }}?w={{ $width * 2 }}&h={{ $height * 2 }}&q=80&f=webp 2x"
                alt="{{ $altText }}" width="{{ $width }}" height="{{ $height }}" loading="lazy"
                decoding="async">
        </div>
    </div>
    <div class="card-body text-center" style="height: 335px; align-content: center;">
        <h6 class="card-title" style="height: 40px; overflow: hidden;">{{ $staff->name }}</h6>
        <h6 class="card-title" style="height: 40px; overflow: hidden;">
            {{ $staff->subTitles->pluck('name')->implode('/') }}</h6>
        <p class="card-title" style="height: 25px; overflow: hidden;">Extra
            Charges:<b>@currency($staff->staff->charges, false)</b></p>
        @if ($staff->staff->location)
            <p class="card-title" style="height: 50px; overflow: hidden;">
                {{ $staff->staff->location }}</p>
        @endif
        @if ($staff->staff->nationality)
            <p class="card-title">
                {{ $staff->staff->nationality }}</p>
        @endif
        <a href="{{ route('staffProfile.show', $staff->id) }}" class="btn btn-block btn-primary">View</a>
        @php
            $rating = $staff->averageRating();
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

        ({{ count($staff->reviews) }} Reviews)
    </div>
</div>
