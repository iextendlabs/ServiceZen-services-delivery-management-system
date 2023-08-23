@extends('layouts.app')
<style>
  a {
    text-decoration: none !important;
  }
</style>
@section('content')
    <div class="row">
        <div class="col-md-12">
        <div class="float-left">
            <h2>FAQs</h2>
        </div>
        <div class="float-right">
            <a class="btn btn-success  float-end" href="{{ route('FAQs.create') }}"> <i class="fa fa-plus"></i></a>
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
            <th class="text-left">Question</th>
            <th class="text-left">Answer</th>
            <th>Action</th>
        </tr>
        @if(count($FAQs))
        @foreach ($FAQs as $FAQ)
        <tr>
            <td>{{ ++$i }}</td>
            <td class="text-left">{{ substr($FAQ->question, 0, 50) }}...</td>
            <td class="text-left">{{ substr($FAQ->answer, 0, 50) }}...</td>
            <td>
                <form action="{{ route('FAQs.destroy',$FAQ->id) }}" method="POST">
                    <a class="btn btn-warning" href="{{ route('FAQs.show',$FAQ->id) }}"><i class="fa fa-eye"></i></a>
                    <a class="btn btn-primary" href="{{ route('FAQs.edit',$FAQ->id) }}"><i class="fa fa-edit"></i></a>
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i></button>
                </form>
            </td>
        </tr>
        @endforeach
        @else
        <tr>
            <td colspan="4" class="text-center">There is no FAQs.</td>
        </tr>
        @endif
    </table>
    {!! $FAQs->links() !!}
@endsection