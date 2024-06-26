@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h3>Affiliate ({{ $total_affiliate }})</h3>
                </div>
                <div class="float-end">
                    @can('affiliate-create')
                        <a class="btn btn-success" href="{{ route('affiliates.create') }}"><i class="fa fa-plus"></i></a>
                    @endcan
                </div>
            </div>
        </div>
        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"
                    aria-label="Close"></button>
            </div>
        @endif
        <div class="row">
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('affiliates.index', array_merge(request()->query(), ['sort' => 'name', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Name</a>
                            @if (request('sort') === 'name')
                                <i
                                    class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th><a class=" ml-2 text-decoration-none"
                                href="{{ route('affiliates.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Email</a>
                            @if (request('sort') === 'email')
                                <i
                                    class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                            @endif
                        </th>
                        <th>Code <br> Commission</th>
                        <th>Number</th>
                        <th>Salary</th>
                        <th>Membership Plan</th>
                        <th>Action</th>
                    </tr>
                    @if (count($affiliates))
                        @foreach ($affiliates as $affiliate)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $affiliate->name }}</td>
                                <td>{{ $affiliate->email }}</td>
                                <td>{{ $affiliate->affiliate->code ?? '' }}
                                    <br>{{ $affiliate->affiliate->commission . '%' ?? '' }}
                                </td>
                                <td>{{ $affiliate->affiliate->number ?? '' }}</td>
                                <td>
                                    @if ($affiliate->affiliate && $affiliate->affiliate->fix_salary)
                                        {{ 'AED' . $affiliate->affiliate->fix_salary }}
                                        (Rs.{{ $pkrRateValue * $affiliate->affiliate->fix_salary ?? '' }})
                                    @endif
                                </td>
                                <td>@if($affiliate->affiliate && $affiliate->affiliate->membershipPlan)
                                    {{ $affiliate->affiliate->membershipPlan->plan_name }} (AED{{ $affiliate->affiliate->membershipPlan->membership_fee }})
                                    @endif
                                </td>
                                <td>
                                    <form id="deleteForm{{ $affiliate->id }}"
                                        action="{{ route('affiliates.destroy', $affiliate->id) }}" method="POST">
                                        <a class="btn btn-warning"
                                            href="{{ route('affiliates.show', $affiliate->id) }}"><i
                                                class="fa fa-eye"></i></a>
                                        @can('affiliate-edit')
                                            <a class="btn btn-primary"
                                                href="{{ route('affiliates.edit', $affiliate->id) }}"><i
                                                    class="fa fa-edit"></i></a>
                                        @endcan
                                        @csrf
                                        @method('DELETE')
                                        @can('affiliate-delete')
                                            <button type="button" class="btn btn-danger"
                                                onclick="confirmDelete('{{ $affiliate->id }}')"><i
                                                    class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no Affiliate.</td>
                        </tr>
                    @endif
                </table>
                {!! $affiliates->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('affiliates.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter_name }}"
                                    class="form-control" placeholder="Name">
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
