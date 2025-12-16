@extends('layouts.app')
@section('content')
@section('page_title')
<h3 class="">CRM</h3>
@endsection
    <div class="container-fluid px-1">
        <div class="row">
            <div class="col-12 mt-1">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">CRM ({{ $total_crm }})</h2>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mt-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>
                                    <i><a class="ml-2 text-dark"
                                        href="{{ route('crms.index', array_merge(request()->query(), ['sort' => 'user_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Customer Name</a></i>
                                    @if (request('sort') === 'user_name')
                                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                                    @endif
                                </th>
                                <th>Customer Email</th>
                                <th>Customer Phone</th>
                                <th>Lead Id</th>
                                <th>Pipeline Id</th>
                                {{-- <th>Action</th> --}}
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($crms))
                                @foreach ($crms as $crm)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $crm->customer_name }}</td>
                                        <td>{{ $crm->email }}</td>
                                        <td>{{ $crm->phone }}</td>
                                        <td>{{ $crm->accountId }}</td>
                                        <td>{{ $crm->pipelineId }}</td>
                                        {{-- <td></td> --}}
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center">There is no crms.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {!! $crms->links() !!}
        </div>
    </div>
@endsection
