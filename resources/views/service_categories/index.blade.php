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

        .grandchild-category {
            margin-left: 60px;
            margin-top: 10px;
        }

        .category-header {
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toggle-icon {
            cursor: pointer;
            margin-right: 10px;
            font-size: 20px;
        }

        .text-muted small {
            font-size: 0.85rem;
        }

        .category-title-wrapper {
            display: flex;
            align-items: center;
        }
    </style>

    @foreach ($service_categories->where('parent_id', null) as $category)
        <div class="category-node">
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
                        <div class="child-category category-node">
                            <div class="category-header">
                                <div class="category-title-wrapper">
                                    @if ($child->childCategories && $child->childCategories->count())
                                        <span class="toggle-icon" data-bs-toggle="collapse" data-bs-target="#grand-children-{{ $child->id }}" aria-expanded="false">+</span>
                                    @endif
                                    <div>
                                        {{ $child->title }}
                                        @if ($child->feature)
                                            <span class="badge bg-success">Featured</span>
                                        @endif
                                        @if ($child->feature_on_bottom)
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
                                            <li><a class="dropdown-item" href="{{ route('FAQs.create', ['category_id' => $child->id]) }}">Add FAQs</a></li>
                                        @endcan
                                        @if($child->status)
                                            <li><a class="dropdown-item" href="https://lipslay.com/category/{{ $child->slug }}" target="_blank">View</a></li>
                                        @endif
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

                            @if ($child->childCategories && $child->childCategories->count())
                                <div id="grand-children-{{ $child->id }}" class="collapse mt-2">
                                    @foreach ($child->childCategories as $grandchild)
                                        <div class="grandchild-category category-node">
                                            <div class="category-header">
                                                <div>
                                                    {{ $grandchild->title }}
                                                    @if ($grandchild->feature)
                                                        <span class="badge bg-success">Featured</span>
                                                    @endif
                                                    @if ($grandchild->feature_on_bottom)
                                                        <span class="badge bg-info">Bottom</span>
                                                    @endif
                                                </div>
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Actions
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        @can('FAQs-create')
                                                            <li><a class="dropdown-item" href="{{ route('FAQs.create', ['category_id' => $grandchild->id]) }}">Add FAQs</a></li>
                                                        @endcan
                                                        @if($grandchild->status)
                                                            <li><a class="dropdown-item" href="https://lipslay.com/category/{{ $grandchild->slug }}" target="_blank">View</a></li>
                                                        @endif
                                                        @can('service-category-edit')
                                                            <li><a class="dropdown-item" href="{{ route('serviceCategories.edit', $grandchild->id) }}">Edit</a></li>
                                                        @endcan
                                                        @can('service-category-delete')
                                                            <li>
                                                                <form action="{{ route('serviceCategories.destroy', $grandchild->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
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
                                                Status: {{ $grandchild->status ? 'Enabled' : 'Disabled' }} |
                                                Type: {{ $grandchild->type }} |
                                                Sort Order: {{ $grandchild->sort }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll('.toggle-icon').forEach(function (icon) {
            icon.addEventListener('click', function () {
                const targetId = icon.getAttribute('data-bs-target');
                const targetEl = document.querySelector(targetId);
                if (targetEl.classList.contains('show')) {
                    icon.textContent = '+';
                } else {
                    icon.textContent = '-';
                }
            });

            const collapseEl = document.querySelector(icon.getAttribute('data-bs-target'));
            collapseEl.addEventListener('shown.bs.collapse', () => {
                icon.textContent = '-';
            });
            collapseEl.addEventListener('hidden.bs.collapse', () => {
                icon.textContent = '+';
            });
        });
    });
</script>
@endpush
@endsection