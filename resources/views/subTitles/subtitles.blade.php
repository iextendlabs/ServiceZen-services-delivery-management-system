@props(['subTitle', 'level' => 0])

@php
    $marginLeft = $level * 30;
@endphp

<div class="subTitle-node" style="margin-left: {{ $marginLeft }}px;" data-name="{{ strtolower($subTitle->name) }}">
    <div class="subTitle-header">
        <div class="subTitle-title-wrapper">
            @if ($subTitle->children && $subTitle->children->count())
                <span class="toggle-icon" data-bs-toggle="collapse" data-bs-target="#children-{{ $subTitle->id }}"
                    aria-expanded="false">+</span>
            @endif
            <div>
                {{ $subTitle->name }}
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                aria-expanded="false">
                Actions
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @can('staff-designation-edit')
                    <li><a class="dropdown-item" href="{{ route('subTitles.edit', $subTitle->id) }}">Edit</a></li>
                @endcan
                @can('staff-designation-delete')
                    <li>
                        <form action="{{ route('subTitles.destroy', $subTitle->id) }}" method="POST"
                            onsubmit="return confirm('Are you sure?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">Delete</button>
                        </form>
                    </li>
                @endcan
            </ul>
        </div>
    </div>

    @if ($subTitle->children && $subTitle->children->count())
        <div id="children-{{ $subTitle->id }}" class="collapse mt-2">
            @foreach ($subTitle->children as $child)
                @include('subTitles.subtitles', ['subTitle' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
