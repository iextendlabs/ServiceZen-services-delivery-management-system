@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Service Categories</h3>
        @can('service-category-create')
            <a class="btn btn-success" href="{{ route('serviceCategories.create') }}">Add Category</a>
        @endcan
    </div>

    <div class="mb-3">
        <input type="text" id="categorySearch" class="form-control" placeholder="Search categories...">
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
        @include('service_categories.categories', ['category' => $category, 'level' => 0])
    @endforeach
</div>

@section('scripts')
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
<script>
    $(document).ready(function () {
        $('#categorySearch').on('keyup', function () {
            let searchText = $(this).val().toLowerCase().trim();

            if (searchText === '') {
                $('.category-node').show();
                $('.collapse').collapse('hide');
                $('.toggle-icon').text('+');
                return;
            }

            $('.category-node').hide();

            $('.category-node').filter(function () {
                return $(this).data('title').includes(searchText);
            }).each(function () {
                $(this).show();

                $(this).parents('.category-node').show();

                $(this).parents('.collapse').each(function () {
                    $(this).collapse('show');
                    let toggleIcon = $('[data-bs-target="#' + $(this).attr('id') + '"]');
                    toggleIcon.text('-');
                });
            });
        });
    });
</script>
@endsection
@endsection