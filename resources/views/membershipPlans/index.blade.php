@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Affiliate Membership Plan ({{ $total_membership_plan }})</h2>
                </div>
                <div class="float-end">
                    @can('membership-plan-create')
                        <a class="btn btn-success" href="{{ route('membershipPlans.create') }}"><i class="fa fa-plus"></i></a>
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
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('membershipPlans.index', array_merge(request()->query(), ['sort' => 'plan_name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Plan Name</a>
                            @if (request('sort') === 'plan_name')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('membershipPlans.index', array_merge(request()->query(), ['sort' => 'membership_fee', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Membership Fee</a>
                            @if (request('sort') === 'membership_fee')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('membershipPlans.index', array_merge(request()->query(), ['sort' => 'expiry_date', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Expiry Date</a>
                            @if (request('sort') === 'expiry_date')
                                <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                            href="{{ route('membershipPlans.index', array_merge(request()->query(), ['sort' => 'status', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Status</a>
                        @if (request('sort') === 'status')
                            <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                        @endif
                    </th>
                        <th>Action</th>
                    </tr>
                    @if (count($membership_plans))
                        @foreach ($membership_plans as $membership_plan)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $membership_plan->plan_name }}</td>
                                <td>{{ $membership_plan->membership_fee }}</td>
                                <td>{{ $membership_plan->expiry_date }}</td>
                                <td>{{ $membership_plan->status == 1 ? "Enable" : "Disable"  }}</td>
                                <td>
                                    <form id="deleteForm{{ $membership_plan->id }}"
                                        action="{{ route('membershipPlans.destroy', $membership_plan->id) }}" method="POST">
                                        <a class="btn btn-warning" href="{{ route('membershipPlans.show', $membership_plan->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('membership-plan-edit')
                                            <a class="btn btn-primary" href="{{ route('membershipPlans.edit', $membership_plan->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('membership-plan-delete')
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete('{{ $membership_plan->id }}')"><i
                                                    class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no membership plan.</td>
                        </tr>
                    @endif
                </table>
                {!! $membership_plans->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('membershipPlans.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Plan Name:</strong>
                                <input type="text" name="plan_name" value="{{ $filter['plan_name'] }}" class="form-control"
                                    placeholder="Plan Name">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Membership Fee:</strong>
                                <input type="number" name="membership_fee" value="{{ $filter['membership_fee'] }}"
                                    class="form-control" placeholder="Membership Fee">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Expiry Date:</strong>
                                <input type="date" name="expiry_date" value="{{ $filter['expiry_date'] }}"
                                    class="form-control" placeholder="Expiry Date">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option></option>
                                    <option value="1" @if ($filter['status'] == '1') selected @endif>Enable</option>
                                    <option value="0" @if ($filter['status'] == '0') selected @endif>Disable</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">Filter</button>
                        </div>
                    </div>
                </form>
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
