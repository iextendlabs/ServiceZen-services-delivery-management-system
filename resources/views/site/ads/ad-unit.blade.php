@if(config('services.adsense.enabled'))
    @if($slot)
        <div class="ad-unit my-4">
            <ins class="adsbygoogle"
                 style="{{ $style }}"
                 data-ad-client="{{ config('services.adsense.client_id') }}"
                 data-ad-slot="{{ $slot }}"
                 data-ad-format="{{ $format }}"
                 data-full-width-responsive="true"></ins>
            <script>
                 (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
        </div>
    @else
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({
                google_ad_client: "{{ config('services.adsense.client_id') }}",
                enable_page_level_ads: true
            });
        </script>
    @endif
@endif