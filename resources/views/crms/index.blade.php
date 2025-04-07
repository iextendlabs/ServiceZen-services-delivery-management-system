@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="float-left">
                    <h2>CRM ({{ $total_crm }})</h2>
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
                        href="{{ route('crms.index', array_merge(request()->query(), ['sort' => 'user_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Customer Name</a>
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
            @if (count($crms))
                @foreach ($crms as $crm)
                    <tr>
                        <td>{{ ++$i }}</td>
                        <td class="text-left">{{ $crm->customer_name }}</td>
                        <td class="text-left">{{ $crm->email }}</td>
                        <td class="text-left">{{ $crm->phone }}</td>
                        <td class="text-left">{{ $crm->accountId }}</td>
                        <td class="text-left">{{ $crm->pipelineId }}</td>
                        {{-- <td></td> --}}
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="6" class="text-center">There is no crms.</td>
                </tr>
            @endif
        </table>
        {!! $crms->links() !!}
    </div>
@endsection
