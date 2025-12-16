<style>
    /* Modern service card styling used across site */
    .service-box .card {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(16,24,40,0.04);
        transition: transform .14s ease, box-shadow .14s ease, border-color .14s ease;
        background: #fff;
    }

    .service-box .card:hover {
        transform: translateY(-6px);
        box-shadow: 0 18px 40px rgba(99,102,241,0.12);
        border-color: rgba(107,33,168,0.18);
    }

    /* top purple band under the image to match sample */
    .service-box .top-band {
        height: 14px;
        background: linear-gradient(90deg,#fbf2ff,#f6f0ff);
    }

    .service-box .image-hero {
        background: linear-gradient(180deg,#fbf2ff,#fff); /* pale purple top */
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding-top: 6px;
        overflow: hidden;
    }

    .service-box .image-hero img {
        display: block; width: 100%; height: 180px; object-fit: cover;
        border-top-left-radius: 8px; border-top-right-radius: 8px;
    }

    .service-box .position-relative { position: relative; }

    .service-box .image-overlay {
        position: absolute; inset: 0; display:flex; align-items:center; justify-content:center;
        background: linear-gradient(0deg, rgba(99,102,241,0.06), rgba(99,102,241,0.06));
        color: #6b21a8; font-weight: 700; font-size: 18px; opacity: 0; transition: opacity .16s ease;
    }

    .service-box .card:hover .image-overlay { opacity: 1; }

    .service-box .service-name { color: #111827; margin: 12px 0 0; font-weight: 600; }
    .service-box .card:hover .service-name { color: #6b21a8; }

    .service-box .title-band { background: #fff; padding: 10px 12px; border-bottom: 1px solid #f3f4f6; }

    .service-box .card-body { padding: 12px 16px 16px; }

    .service-box .service-meta { font-size: 14px; color: #6b7280; }

    .service-box .btn { border-radius: 8px; }
</style>

<div class="service-box">
    <div class="card mb-4 box-shadow">
        <a href="/service/{{ $service->slug }}">
            @php
                $imagePath = 'service-images/' . $service->image;
                $altText = $service->image_alt ?? $service->name;
                $width = 420;
                $height = 260;
            @endphp

            <div class="image-hero position-relative">
                <img class="card-img-top img-fluid" src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
                    srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x, {{ url('img/' . $imagePath) }}?w={{ $width * 2 }}&h={{ $height * 2 }}&q=80&f=webp 2x"
                    alt="{{ $altText }}" width="{{ $width }}" height="{{ $height }}" loading="lazy" decoding="async">

                <div class="image-overlay" aria-hidden="true">
                    <i class="fa fa-search mr-2" aria-hidden="true"></i> View
                </div>
            </div>
            <div class="top-band" aria-hidden="true"></div>
        </a>

            <div class="title-band">
                <p class="card-text service-name text-center">{{ $service->name }}</p>
            </div>

        <div class="card-body text-center">

            <div class="d-flex justify-content-between align-items-center mt-2 mb-3 px-2 service-meta">
                <div>
                    @if (isset($service->discount))
                        <s class="text-muted">@currency($service->price, false, true)</s>
                        <span class="ml-1 font-weight-bold">@currency($service->discount, false, true)</span>
                    @else
                        <span class="font-weight-bold">@currency($service->price, false, true)</span>
                    @endif
                </div>
                <div>
                    @if ($service->duration)
                        <small class="text-muted"><i class="fa fa-clock mr-1"></i>{{ $service->duration }}</small>
                    @endif
                </div>
            </div>

            <div class="d-grid">
                @if ($service->quote == 1)
                    <button onclick="openQuotePopup('{{ $service->id }}')" type="button" class="btn btn-outline-primary mb-2">
                        <i class="fa fa-envelope mr-2"></i> Request a Quote
                    </button>
                @elseif (count($service->serviceOption) > 0)
                    <a href="/service/{{ $service->slug }}" class="btn btn-primary mb-2">
                        <i class="fa fa-info-circle mr-2"></i> Details
                    </a>
                @else
                    <button onclick="openBookingPopup('{{ $service->id }}')" type="button" class="btn btn-primary mb-2">
                        <i class="fa fa-calendar-alt mr-2"></i> Book Now
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
