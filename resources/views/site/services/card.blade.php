<div class="service-box">
    <div class="card mb-4 box-shadow">
        <a href="/service/{{ $service->slug }}">
            <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
            @php
                $imagePath = 'service-images/' . $service->image;
                $altText = $service->image_alt ?? $service->name;
                $width = 298;
                $height = 250;
            @endphp

            <img class="card-img-top img-fluid"
                src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
                srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x,
                                 {{ url('img/' . $imagePath) }}?w={{ $width * 2 }}&h={{ $height * 2 }}&q=80&f=webp 2x"
                alt="{{ $altText }}" width="{{ $width }}" height="{{ $height }}" loading="lazy"
                decoding="async">
        </a>
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <small class="text-muted service-box-price">
                    @if (isset($service->discount))
                        <s>
                    @endif
                    @currency($service->price, false, true)
                    @if (isset($service->discount))
                        </s>
                    @endif
                    @if (isset($service->discount))
                        <b class="discount"> @currency($service->discount, false, true)</b>
                    @endif
                </small>
                @if ($service->duration)
                    <small class="text-muted service-box-time"><i class="fa fa-clock"> </i>
                        {{ $service->duration }}</small>
                @endif
            </div>
            @if ($service->quote == 1)
                <button style="margin-top: 1em;" onclick="openQuotePopup('{{ $service->id }}')" type="button"
                    class="btn btn-block btn-warning"> Request a Quote</button>
            @elseif (count($service->serviceOption) > 0)
                <a style="margin-top: 1em; color:#fff" href="/service/{{ $service->slug }}" type="button"
                    class="btn btn-block btn-primary">Book Now</a>
            @else
                <button onclick="openBookingPopup('{{ $service->id }}')" type="button"
                    class="btn btn-block btn-primary"> Book Now</button>
            @endif
        </div>
    </div>
</div>
