@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="float-left">
                <h2>Review</h2>
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
            <th class="text-left">User</th>
            <th class="text-left">Rating</th>
            <th>Action</th>
        </tr>
        @if(count($reviews))
        @foreach ($reviews as $review)
        <tr>
            <td>{{ ++$i }}</td>
            <td class="text-left">{{ $review->user_name }}</td>
            <td class="text-left">
                @for($a = 1; $a <= 5; $a++) @if($a <=$review->rating)
                    <span class="text-warning">&#9733;</span>
                    @else
                    <span class="text-muted">&#9734;</span>
                    @endif
                    @endfor</td>
            <td>
                <form action="{{ route('reviews.destroy',$review->id) }}" method="POST">
                    <a class="btn btn-warning" href="{{ route('reviews.show',$review->id) }}"><i class="fa fa-eye"></i></a>
                    @can('review-edit')
                    <a class="btn btn-primary" href="{{ route('reviews.edit',$review->id) }}"><i class="fa fa-edit"></i></a>
                    @endcan
                    @csrf
                    @method('DELETE')
                    @can('review-delete')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
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
@endsection