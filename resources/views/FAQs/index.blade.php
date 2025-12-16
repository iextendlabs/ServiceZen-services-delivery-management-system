@extends('layouts.app')
<style>
    a {
        text-decoration: none !important;
    }
</style>
@section('content')
    @section('page_title')
    <h3 class="">FAQs</h3>
    @endsection
    <div class="container">
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="float-right">
                            @can('FAQs-create')
                                <a class="btn text-dark  float-end" href="{{ route('FAQs.create') }}"> <i class="fa fa-plus"></i> New FAQ</a>
                            @endcan
                        </div>                        
                        <h3>Filter</h3>
                        <hr>
                        <form action="{{ route('FAQs.index') }}" method="GET" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <label class="small text-muted font-weight-medium mb-1">Question:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend w-100">
                                            <span class="input-group-text bg-white border-right-0"
                                                style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                    class="fas fa-globe text-muted"></i></span>
                                            <input type="text" name="question" value="{{ $filter['question'] }}" class="form-control"
                                                placeholder="Question">
                                        </div>        
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="small text-muted font-weight-medium mb-1">Feature:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend w-100">
                                            <span class="input-group-text bg-white border-right-0"
                                                style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                    class="fas fa-star text-muted"></i></span>
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
                                </div>                                                                
                                <div class="col-md-6">
                                    <label class="small text-muted font-weight-medium mb-1">Service:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend w-100">
                                            <span class="input-group-text bg-white border-right-0"
                                                style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                    class="fas fa-globe text-muted"></i></span>
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
                                </div>                                
                                <div class="col-md-8 mt-2">
                                    <label class="small text-muted font-weight-medium mb-1">Category:</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend w-100">
                                            <span class="input-group-text bg-white border-right-0"
                                                style="border-radius: 0.75rem 0 0 0.75rem;"><i
                                                    class="fas fa-globe text-muted"></i></span>
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
                                <div class="col-md-4">
                                    <div class="d-flex flex-wrap justify-content-md-start mt-4">
                                        <a href="{{ url()->current() }}" class="btn btn-md btn-light border mr-2 font-weight-medium"><i class="fa fa-undo"></i> Reset</a>
                                        <button type="submit" class="btn btn-md btn-primary shadow-sm font-weight-bold"><i class="fa fa-filter"></i> Filter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <h3>FAQ ({{ $total_faq }})</h3>
            <div class="col-md-12">
                <table class="table table-bordered">
                    <tr class="text-center bg-white text-white">
                        <th class="text-primary">Sr#</th>
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
                        <th class="text-primary">Action</th>
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
                                <td class="text-center">
                                    <form id="deleteForm{{ $FAQ->id }}" action="{{ route('FAQs.destroy', $FAQ->id) }}" method="POST" class="d-flex justify-content-center align-items-center gap-2 m-0">
                                        <a class="btn btn-sm text-dark" href="{{ route('FAQs.show', $FAQ->id) }}"><i class="fa fa-eye"></i></a>
                                        @can('FAQs-edit')
                                            <a class="btn btn-sm text-dark" href="{{ route('FAQs.edit', $FAQ->id) }}"><i class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('FAQs-delete')
                                            <button type="button" onclick="confirmDelete('{{ $FAQ->id }}')" class="btn btn-sm text-danger"><i class="fas fa-trash"></i></button>
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
