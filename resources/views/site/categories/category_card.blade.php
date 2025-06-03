<div class="col-md-4 service-box">
    <div class="card mb-4 box-shadow">
        <a href="{{ route('category.show', $category->slug) }}">
            <p class="card-text service-box-title text-center"><b>{{ $category->title }}</b></p>
            <div class="col-md-12 text-center">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 230px;">
                    @php
                        $imagePath = 'service-category-images/' . $category->image;
                        $altText = $category->image_alt ?? $category->title;
                        $width = 298;
                        $height = 250;
                    @endphp

                    <img class="card-img-top img-fluid"
                        src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
                        srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x,
                                 {{ url('img/' . $imagePath) }}?w={{ $width * 2 }}&h={{ $height * 2 }}&q=80&f=webp 2x"
                        alt="{{ $altText }}" width="{{ $width }}" height="{{ $height }}"
                        loading="lazy" decoding="async">
                </div>
            </div>
        </a>
    </div>
</div>
