<div class="w-full sm:w-1/2 md:w-1/3 lg:w-1/4 p-3">
  <a href="{{ route('category.show', $category->slug) }}" 
     class="group block bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition-all duration-300 ease-in-out transform hover:-translate-y-1">
    
    <!-- Image Section -->
    <div class="relative flex items-center justify-center bg-gradient-to-br from-pink-50 to-purple-100 h-60">
      @php
          $imagePath = 'service-category-images/' . $category->image;
          $altText = $category->image_alt ?? $category->title;
          $width = 298;
          $height = 250;
      @endphp
      <img class="w-full h-full object-contain transition-transform duration-300 group-hover:scale-105"
           src="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp"
           srcset="{{ url('img/' . $imagePath) }}?w={{ $width }}&h={{ $height }}&q=80&f=webp 1x,
                    {{ url('img/' . $imagePath) }}?w={{ $width * 2 }}&h={{ $height * 2 }}&q=80&f=webp 2x"
           alt="{{ $altText }}" width="{{ $width }}" height="{{ $height }}"
           loading="lazy" decoding="async">
      <!-- Gradient overlay -->
      <div class="absolute inset-0 bg-gradient-to-t from-black/20 via-transparent to-transparent opacity-0 group-hover:opacity-50 transition-opacity duration-300"></div>
    </div>

    <!-- Title -->
    <div class="p-4 text-center">
      <h3 class="text-lg font-semibold text-gray-800 group-hover:text-pink-600 transition-colors duration-300">
        {{ $category->title }}
      </h3>
    </div>
  </a>
</div>
                                            