@props(['category', 'level' => 0])

@php
    $marginLeft = $level * 30;
@endphp

<div class="category-node" style="margin-left: {{ $marginLeft }}px;" data-title="{{ strtolower($category->title) }}">
    <div class="category-header">
        <div class="category-title-wrapper">
            @if ($category->childCategories && $category->childCategories->count())
                <span class="toggle-icon" data-bs-toggle="collapse" data-bs-target="#children-{{ $category->id }}" aria-expanded="false">+</span>
            @endif
            <div>
                {{ $category->title }}
                @if ($category->feature)
                    <span class="badge bg-success">Featured</span>
                @endif
                @if ($category->feature_on_bottom)
                    <span class="badge bg-info">Bottom</span>
                @endif
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @can('FAQs-create')
                    <li><a class="dropdown-item" href="{{ route('FAQs.create', ['category_id' => $category->id]) }}">Add FAQs</a></li>
                @endcan
                @if($category->status)
                    <li><a class="dropdown-item" href="https://lipslay.com/category/{{ $category->slug }}" target="_blank">View</a></li>
                @endif
                @can('service-category-edit')
                    <li><a class="dropdown-item" href="{{ route('serviceCategories.edit', $category->id) }}">Edit</a></li>
                @endcan
                @can('service-category-delete')
                    <li>
                        <form action="{{ route('serviceCategories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                        </form>
                    </li>
                @endcan
            </ul>
        </div>
    </div>
    <div class="text-muted small mt-1">
        Status: {{ $category->status ? 'Enabled' : 'Disabled' }} |
        Type: {{ $category->type }} |
        Sort Order: {{ $category->sort }}
    </div>

    @if ($category->childCategories && $category->childCategories->count())
        <div id="children-{{ $category->id }}" class="collapse mt-2">
            @foreach ($category->childCategories as $child)
                @include('service_categories.categories', ['category' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>