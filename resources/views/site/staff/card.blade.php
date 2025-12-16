<style>
    /* Scoped staff card styles (kept in partial so page changes don't break layout) */
    .staff-card { border-radius: 0.6rem; overflow: hidden; box-shadow: 0 6px 18px rgba(0,0,0,0.06); background: #fff; display:flex; flex-direction:column; height:100%; }
    .staff-card:hover { transform: translateY(-6px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
    .staff-card .card-img-top { width:100%; height:180px; object-fit:cover; display:block; }
    /* Ensure card body reserves space so cards align even when one field has long content */
    .staff-card .card-body { padding:1rem; display:flex; flex-direction:column; justify-content:space-between; flex:1 1 auto; min-height: 200px; }
    .staff-card .staff-title { font-weight:700; font-size:1rem; color:#101428; margin-bottom:0.15rem; }
    /* Limit subtitle to 2 lines with ellipsis to avoid extremely long single-line blocks */
    .staff-card .staff-subtitle {
        color:#6c757d; font-size:0.9rem; margin-bottom:0.6rem;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis;
    }
    /* Meta (charges/location) clamp to single line */
    .staff-meta { color:#6c757d; font-size:0.9rem; margin-bottom:0.25rem; display:block; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .staff-card .rating-row { display:flex; align-items:center; gap:0.5rem; margin-bottom:0.5rem; }
    .staff-card .rating-row .rating-value { font-weight:600; color:#343a40; font-size:0.95rem; }
    .staff-card .stars i { font-size: 14px; }
    .staff-card .chips { margin-top:0.5rem; display:flex; flex-wrap:wrap; gap:0.4rem; }
    .staff-card .chip { background:#f3e8ff; color:#6f42c1; padding:0.28rem 0.6rem; font-size:0.78rem; border-radius:0.6rem; max-width:100%; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .staff-card .view-btn { margin-top:0.6rem; }
</style>

@php
    $imagePath = 'staff-images/' . ($staff->staff->image ?? '');
    $altText = $staff->staff->image_alt ?? $staff->name;
    $width = 800; $height = 480;
@endphp

<div class="card staff-card mb-3" style="border: 1px solid #e5e7eb; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07); overflow: hidden; height: 100%;">
    @if(!empty($staff->staff->image))
        <img class="card-img-top" style="height: 260px; width: 100%; inset: 0px; color: transparent;" src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
             srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x, {{ url('img/' . $imagePath) }}?w={{ $width*2 }}&h={{ $height*2 }}&q=80&f=webp 2x"
             alt="{{ $altText }}" loading="lazy" decoding="async">
    @else
        <div style="width:100%;height:180px;background:#f1f3f5;display:flex;align-items:center;justify-content:center;color:#adb5bd;">No Image</div>
    @endif

    <div class="card-body" style="padding: 28px 24px; display:flex; flex-direction:column; gap:12px; box-sizing:border-box; height:255px;">
        <div>
            <div class="staff-title">{{ $staff->name }}</div>
            <div class="staff-subtitle">{{ $staff->subTitles->pluck('name')->implode(' / ') }}</div>

            <div class="staff-meta">Extra Charges: <strong>@currency($staff->staff->charges ?? 0, false)</strong></div>
            @if(!empty($staff->staff->location))
                <div class="staff-meta">{{ $staff->staff->location }}</div>
            @endif
        </div>

        <div>
            <a href="{{ route('staffProfile.show', $staff->id) }}"> 
                <div class="rating-row">
                    @php
                        $rating = (float) ($staff->averageRating() ?? 0);
                        $fullStars = floor($rating);
                        $halfStar = ($rating - $fullStars) >= 0.5;
                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                    @endphp

                    <div class="stars" aria-hidden="true">
                        @for ($i = 0; $i < $fullStars; $i++)
                            <i class="fas fa-star text-warning"></i>
                        @endfor
                        @if ($halfStar)
                            <i class="fas fa-star-half-alt text-warning"></i>
                        @endif
                        @for ($i = 0; $i < $emptyStars; $i++)
                            <i class="far fa-star text-muted"></i>
                        @endfor
                    </div>
                    <div class="rating-value">{{ number_format($rating, 2) }}</div>
                    <div class="text-muted">({{ count($staff->reviews ?? []) }})</div>
                </div>

                @php
                    $services = [];
                    if(isset($staff->services) && $staff->services->count()) { $services = $staff->services; }
                    elseif(isset($staff->staff->services) && $staff->staff->services->count()) { $services = $staff->staff->services; }
                @endphp

                @if(!empty($services))
                    <div class="chips">
                        @foreach($services->take(4) as $s)
                            <div class="chip">{{ $s->name }}</div>
                        @endforeach
                    </div>
                @endif
            </a>
        </div>
    </div>
</div>
