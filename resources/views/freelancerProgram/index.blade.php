@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <div class="float-start">
                    <h2>Freelancer Program Joinee</h2>
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
                                <td>{{ $user->freelancer_program === '1' ? 'Accepted' : 'Rejected' }}</td>
                                <td>@if($user->staff && $user->staff->membershipPlan)
                                    {{ $user->staff->membershipPlan->plan_name }} (AED{{ $user->staff->membershipPlan->membership_fee }})
                                    @endif
                                </td>
                                <td>

                                    <form id="deleteForm{{ $user->id }}"
                                        action="{{ route('freelancerProgram.destroy', $user->id) }}" method="POST">
                                        @if ($user->freelancer_program === '0')
                                            @can('freelancer-program-edit')
                                            <a class="btn btn-success"
                                                href="{{ route('freelancerProgram.edit', $user->id) }}?status=Accepted">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            @endcan
                                        @elseif ($user->freelancer_program === '1')
                                            @can('freelancer-program-edit')
                                            <a class="btn btn-danger"
                                                href="{{ route('freelancerProgram.edit', $user->id) }}?status=Rejected">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                            <a class="btn btn-primary"
                                                href="{{ route('serviceStaff.edit', $user->id) }}?freelancer_join=1">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            <a class="btn btn-warning"
                                                href="{{ route('serviceStaff.show', $user->id) }}?freelancer_join=1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        @csrf
                                        @can('freelancer-program-delete')
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete('{{ $user->id }}')"
                                            class="btn btn-danger"><i class="fa fa-trash"></i></button>
                                        @endcan
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="text-center">There is no New Freelancer Joinee.</td>
                        </tr>
                    @endif
                </table>
                {!! $users->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('freelancerProgram.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option value="">-- Select Status --</option>
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
