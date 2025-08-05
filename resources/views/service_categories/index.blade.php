@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Service Categories</h3>
        @can('service-category-create')
            <a class="btn btn-success" href="{{ route('serviceCategories.create') }}">Add Category</a>
        @endcan
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <style>
        .category-node {
            border: 1px solid #ccc;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .child-category {
            margin-left: 30px;
            margin-top: 10px;
        }

        .category-header {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .dropdown-toggle::after {
            margin-left: 0.5em;
        }

        .text-muted small {
            font-size: 0.85rem;
        }
    </style>

    {{-- Category Tree View --}}
    @foreach ($service_categories->where('parent_id', null) as $category)
        <div class="category-node">
            <div class="category-header">
                <div>
                    {{ $category->title }}
                    @if ($category->feature)
                        <span class="badge bg-success">Featured</span>
                    @endif
                    @if ($category->feature_on_bottom)
                        <span class="badge bg-info">Bottom</span>
                    @endif
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Actions
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @can('FAQs-create')
                            <li><a class="dropdown-item" href="{{ route('FAQs.create', ['category_id' => $category->id]) }}">Add FAQs</a></li>
                        @endcan
                        <li><a class="dropdown-item" href="{{ route('serviceCategories.show', $category->id) }}">View</a></li>
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

            {{-- Render children --}}
            @if ($category->childCategories && $category->childCategories->count())
                @foreach ($category->childCategories as $child)
                    <div class="child-category category-node">
                        <div class="category-header">
                            <div>
                                {{ $child->title }}
                                @if ($child->feature)
                                    <span class="badge bg-success">Featured</span>
                                @endif
                                @if ($child->feature_on_bottom)
                                    <span class="badge bg-info">Bottom</span>
                                @endif
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Actions
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @can('FAQs-create')
                                        <li><a class="dropdown-item" href="{{ route('FAQs.create', ['category_id' => $child->id]) }}">Add FAQs</a></li>
                                    @endcan
                                    <li><a class="dropdown-item" href="{{ route('serviceCategories.show', $child->id) }}">View</a></li>
                                    @can('service-category-edit')
                                        <li><a class="dropdown-item" href="{{ route('serviceCategories.edit', $child->id) }}">Edit</a></li>
                                    @endcan
                                    @can('service-category-delete')
                                        <li>
                                            <form action="{{ route('serviceCategories.destroy', $child->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                            Status: {{ $child->status ? 'Enabled' : 'Disabled' }} |
                            Type: {{ $child->type }} |
                            Sort Order: {{ $child->sort }}
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @endforeach
</div>

{{-- Bootstrap 5 required --}}
@push('scripts')
<script>
    // If you're not using Laravel Mix, include Bootstrap's JS manually in layout
</script>
@endpush
@endsection