@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Affiliate Program Joinee</h2>
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
                        <th>Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Membership Plan</th>
                        <th>Action</th>
                    </tr>
                    @if (count($users))
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if ($user->affiliate_program === '1')
                                        <span class="badge bg-success">Accepted</span>
                                    @elseif($user->affiliate_program === '0')
                                        @if ($user->affiliate)
                                            <span class="badge bg-warning text-dark">New</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    @endif
                                </td>
                                <td>@if($user->affiliate && $user->affiliate->membershipPlan)
                                    {{ $user->affiliate->membershipPlan->plan_name }} (AED{{ $user->affiliate->membershipPlan->membership_fee }})
                                    @endif
                                </td>
                                <td>
                                    <form id="deleteForm{{ $user->id }}"
                                        action="{{ route('affiliateProgram.destroy', $user->id) }}" method="POST">
                                        @if ($user->affiliate_program === '0')
                                            @can('affiliate-program-edit')
                                            <a class="btn btn-success"
                                                href="{{ route('affiliateProgram.edit', $user->id) }}?status=Accepted">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            @endcan
                                        @elseif ($user->affiliate_program === '1')
                                            @can('affiliate-program-edit')
                                            <a class="btn btn-danger"
                                                href="{{ route('affiliateProgram.edit', $user->id) }}?status=Rejected">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                            <a class="btn btn-primary" href="{{ route('affiliates.edit', $user->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            <a class="btn btn-warning" href="{{ route('affiliates.show', $user->id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif

                                        @csrf
                                        @method('DELETE')
                                        @can('affiliate-program-delete')
                                        <button type="button" class="btn btn-danger"
                                            onclick="confirmDelete('{{ $user->id }}')"><i
                                                class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no New Affiliate Joinee.</td>
                        </tr>
                    @endif
                </table>
                {!! $users->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('affiliateProgram.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option value="">-- Select Status --</option>
                                    <option value="2" @if ($filter_status === '2') selected @endif>New
                                    <option value="1" @if ($filter_status === '1') selected @endif>Accepted
                                    </option>
                                    <option value="0" @if ($filter_status === '0') selected @endif>Rejected
                                    </option>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Name:</strong>
                                <input type="text" name="name" value="{{ $filter_name }}" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Email:</strong>
                                <input type="email" name="email" value="{{ $filter_email }}" class="form-control">
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
