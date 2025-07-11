@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <h2>FAQs</h2>
                </div>
                <div class="float-right">
                    @can('FAQs-create')
                        <a class="btn btn-success  float-end" href="{{ route('FAQs.create') }}"> <i class="fa fa-plus"></i></a>
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
        <div class="row">
            <div class="col-md-12">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('FAQs.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Question:</strong>
                                <input type="text" name="question" value="{{ $filter['question'] }}" class="form-control"
                                    placeholder="Question">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Feature:</strong>
                                <select name="feature" class="form-control">
                                    <option value="">-- All --</option>
                                    <option value="1"
                                        {{ isset($filter['feature']) && $filter['feature'] === '1' ? 'selected' : '' }}>Yes
                                    </option>
                                    <option value="0"
                                        {{ isset($filter['feature']) && $filter['feature'] === '0' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Services:</strong>
                                <select name="service_id" class="form-control">
                                    <option></option>
                                    @foreach ($services as $service)
                                        @if ($service->id == $filter['service_id'])
                                            <option value="{{ $service->id }}" selected>{{ $service->name }}</option>
                                        @else
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <strong>Category:</strong>
                                <select name="category_id" class="form-control">
                                    <option></option>
                                    @foreach ($categories as $category)
                                        @if ($category->id == $filter['category_id'])
                                            <option value="{{ $category->id }}" selected>{{ $category->title }}</option>
                                        @else
                                            <option value="{{ $category->id }}">{{ $category->title }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 offset-md-8">
                            <div class="d-flex flex-wrap justify-content-md-end">
                                <div class="col-md-3 mb-3">
                                    <a href="{{ url()->current() }}" class="btn btn-lg btn-secondary">Reset</a>
                                </div>
                                <div class="col-md-9 mb-3">
                                    <button type="submit" class="btn btn-lg btn-block btn-primary">Filter</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <h3>FAQ ({{ $total_faq }})</h3>
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('FAQs.index', array_merge(request()->query(), ['sort' => 'question', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Question</a>
                            @if (request('sort') === 'question')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('FAQs.index', array_merge(request()->query(), ['sort' => 'answer', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Answer</a>
                            @if (request('sort') === 'answer')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('FAQs.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                            @if (request('sort') === 'status')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('FAQs.index', array_merge(request()->query(), ['sort' => 'feature', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Feature</a>
                            @if (request('sort') === 'feature')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Action</th>
                    </tr>
                    @if (count($FAQs))
                        @foreach ($FAQs as $FAQ)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td class="text-left">{{ substr($FAQ->question, 0, 50) }}...</td>
                                <td class="text-left">{{ substr($FAQ->answer, 0, 50) }}...</td>
                                <td class="text-left">
                                    @if ($FAQ->status == 1)
                                        Enable
                                    @else
                                        Disable
                                    @endif
                                </td>
                                <td>{{ $FAQ->feature ? 'Yes' : 'No' }}</td>
                                <td>
                                    <form id="deleteForm{{ $FAQ->id }}"
                                        action="{{ route('FAQs.destroy', $FAQ->id) }}" method="POST">
                                        <a class="btn btn-warning" href="{{ route('FAQs.show', $FAQ->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('FAQs-edit')
                                            <a class="btn btn-primary" href="{{ route('FAQs.edit', $FAQ->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('FAQs-delete')
                                            <button type="button" onclick="confirmDelete('{{ $FAQ->id }}')"
                                                class="btn btn-danger"><i class="fas fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no FAQs.</td>
                        </tr>
                    @endif
                </table>
                {!! $FAQs->links() !!}
            </div>
        </div>
    </div>
    <script>
        function confirmDelete(Id) {
            var result = confirm("Are you sure you want to delete this Item?");
            if (result) {
                document.getElementById('deleteForm' + Id).submit();
            }
        }
    </script>
    <script>
        $(document).ready(function() {
            function checkTableResponsive() {
                var viewportWidth = $(window).width();
                var $table = $('table');

                if (viewportWidth < 768) {
                    $table.addClass('table-responsive');
                } else {
                    $table.removeClass('table-responsive');
                }
            }

            checkTableResponsive();

            $(window).resize(function() {
                checkTableResponsive();
            });
        });
    </script>
@endsection
