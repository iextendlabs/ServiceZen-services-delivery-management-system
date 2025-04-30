<div class="service-box">
    <div class="card mb-4 box-shadow">
        <a href="/service/{{ $service->slug }}">
            <p class="card-text service-box-title text-center"><b>{{ $service->name }}</b></p>
            <img class="card-img-top" src="{{ url('img/service-images/' . $service->image) }}?w=298&h=250"
                alt="{{ $service->name }}">
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
