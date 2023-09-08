    @foreach ($children as $child)
            @include('site.categories.category_card', ['category' => $child])
            @if ($child->childCategories->count() > 0)
                @include('site.categories..child_categories', ['children' => $child->childCategories])
            @endif
    @endforeach
