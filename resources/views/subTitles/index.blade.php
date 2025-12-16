@extends('layouts.app')
@section('content')
@section('page_title')
<h3 class="">Sub Titles</h3>
@endsection
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3>Sub Titles/Designation</h3>
            @can('staff-designation-create')
                <a class="btn text-dark" href="{{ route('subTitles.create') }}"><i class="fas fa-plus"></i> Add SubTitle</a>
            @endcan
        </div>

        <div class="mb-3">
            <input type="text" id="subtitleSearch" class="form-control" placeholder="Search subtitles...">
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
            .subTitle-node {
                border: 1px solid #ccc;
                padding: 15px;
                margin-bottom: 10px;
                border-radius: 5px;
            }

            .child-subtitle {
                margin-left: 30px;
                margin-top: 10px;
            }

            .grandchild-subtitle {
                margin-left: 60px;
                margin-top: 10px;
            }

            .subtitle-header {
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

            .subtitle-title-wrapper {
                display: flex;
                align-items: center;
            }
        </style>

        @foreach ($subTitles->where('parent_id', null) as $subTitle)
            @include('subTitles.subtitles', ['subTitle' => $subTitle, 'level' => 0])
        @endforeach
    </div>

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('.toggle-icon').forEach(function(icon) {
                icon.addEventListener('click', function() {
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
        $(document).ready(function() {
            $('#subtitleSearch').on('keyup', function() {
                let searchText = $(this).val().toLowerCase().trim();

                if (searchText === '') {
                    $('.subTitle-node').show();
                    $('.collapse').collapse('hide');
                    $('.toggle-icon').text('+');
                    return;
                }

                $('.subTitle-node').hide();

                // Show nodes that match search
                $('.subTitle-node').filter(function() {
                    return $(this).data('name').includes(searchText);
                }).each(function() {
                    $(this).show();
                    // Show all parents of matched node
                    $(this).parents('.subTitle-node').show();
                    // Expand all parent collapses
                    $(this).parents('.collapse').each(function() {
                        $(this).collapse('show');
                        let toggleIcon = $('[data-bs-target="#' + $(this).attr('id') +
                        '"]');
                        toggleIcon.text('-');
                    });

                    // If this node is a parent, show all its children
                    let collapseId = $(this).find('.toggle-icon').attr('data-bs-target');
                    if (collapseId) {
                        $(collapseId).collapse('show');
                        let toggleIcon = $('[data-bs-target="' + collapseId + '"]');
                        toggleIcon.text('-');
                        $(collapseId).find('.subTitle-node').show();
                    }
                });
            });
        });
    </script>
@endsection
@endsection
