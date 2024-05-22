@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <h2>Review ({{ $total_review }})</h2>
                </div>
                <div class="float-right">
                    @can('review-create')
                        <a class="btn btn-success  float-end" href="{{ route('reviews.create') }}"> <i class="fa fa-plus"></i></a>
                    @endcan
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr>
        <table class="table table-striped table-bordered">
            <tr>
                <th>Sr#</th>
                <th><a class=" ml-2 text-decoration-none"
                        href="{{ route('reviews.index', array_merge(request()->query(), ['sort' => 'user_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">User</a>
                    @if (request('sort') === 'user_name')
                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                    @endif
                </th>
                <th><a class=" ml-2 text-decoration-none"
                    href="{{ route('reviews.index', array_merge(request()->query(), ['sort' => 'rating', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Rating</a>
                @if (request('sort') === 'rating')
                    <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                @endif
            </th>
                <th>Action</th>
            </tr>
            @if (count($reviews))
                @foreach ($reviews as $review)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td class="text-left">{{ $review->user_name }}</td>
                        <td class="text-left">
                            @for ($a = 1; $a <= 5; $a++)
                                @if ($a <= $review->rating)
                                    <span class="text-warning">&#9733;</span>
                                @else
                                    <span class="text-muted">&#9734;</span>
                                @endif
                            @endfor
                        </td>
                        <td>
                            <form id="deleteForm{{ $review->id }}" action="{{ route('reviews.destroy', $review->id) }}"
                                method="POST">
                                <a class="btn btn-warning" href="{{ route('reviews.show', $review->id) }}"><i
                                        class="fa fa-eye"></i></a>
                                @can('review-edit')
                                    <a class="btn btn-primary" href="{{ route('reviews.edit', $review->id) }}"><i
                                            class="fa fa-edit"></i></a>
                                @endcan
                                @csrf
                                @method('DELETE')
                                @can('review-delete')
                                    <button type="button" onclick="confirmDelete('{{ $review->id }}')"
                                        class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                @endcan
                            </form>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="4" class="text-center">There is no reviews.</td>
                </tr>
            @endif
        </table>
        {!! $reviews->links() !!}
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
@endsection
