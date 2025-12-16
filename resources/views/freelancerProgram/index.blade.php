@extends('layouts.app')
@section('content')
    @section('page_title')
    <h3 class="">Freelancer Program Joinee</h3>
    @endsection
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
            <div class="col-md-12">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('freelancerProgram.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small text-muted font-weight-medium mb-1">Status</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-clock text-muted"></i></span>
                                        </div>
                                    <select name="status" class="form-control">
                                        <option value="">-- Select Status --</option>
                                        <option value="2" @if ($filters['status'] === '2') selected @endif>New
                                        <option value="1" @if ($filters['status'] === '1') selected @endif>Accepted
                                        </option>
                                        <option value="0" @if ($filters['status'] === '0') selected @endif>Rejected
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                        {{-- <div class="col-12 col-sm-6 col-lg-4 mb-3">
                                            <label class="small text-muted font-weight-medium mb-1">Status</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-clock text-muted"></i></span>
                                                </div>
                                                <select name="status" class="custom-select" style="border-left: 0;">
                                                    <option value="">Select</option>
                                                    @foreach ($statuses as $status)
                                                        <option value="{{ $status }}" @if ($status == $filter['status']) selected @endif>{{ $status }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div> --}}

                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small text-muted font-weight-medium mb-1">Name</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-user text-muted"></i></span>
                                    </div>
                                    <input type="text" name="name" value="{{ $filters['name'] }}" class="form-control" placeholder="Enter Name">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small text-muted font-weight-medium mb-1">Email</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text bg-white border-right-0" style="border-radius: 0.75rem 0 0 0.75rem;"><i class="fas fa-envelope text-muted"></i></span>
                                    </div>
                                    <input type="email" name="email" id="email-autocomplete" value="{{ $filters['email'] }}" class="form-control" autocomplete="off" placeholder="Enter Email...">
                                    <ul id="email-suggestions" class="list-group" style="position:absolute; z-index:1000; width:100%; display:none; max-height:180px; overflow-y:auto;"></ul>
                                    <style>
                                        #email-suggestions .list-group-item {
                                            cursor: pointer !important;
                                        }
                                        #email-suggestions .list-group-item:hover {
                                            background-color: #f0f0f0;
                                        }
                                    </style>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="small text-muted font-weight-medium mb-1">Freelancer Group:</label>
                                <select name="freelancer_group_id" class="form-control select2">
                                    <option value="">-- Select Freelancer Group --</option>
                                    @foreach ($freelancer_groups as $group)
                                        <option value="{{ $group->id }}" @if ($filters['freelancer_group_id'] == $group->id) selected @endif>{{ $group->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3 my-4 pt-2 text-end">
                            <button type="submit" class="btn btn-primary w-50">Filter</button>
                        </div>
                    </div>
                </form>
            </div>            
            <div class="col-md-12">
                <table class="table table-hover table-bordered">
                    <tr class="table-white bg-primary text-white">
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
                                    @if ($user->freelancer_program === '1')
                                        <span class="badge bg-primary rounded-3">Accepted</span>
                                    @elseif($user->freelancer_program === '0')
                                        @if ($user->staff)
                                            <span class="badge bg-warning rounded-3 text-dark">New</span>
                                        @else
                                            <span class="badge bg-danger">Rejected</span>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    @if ($user->staff && $user->staff->membershipPlan)
                                        {{ $user->staff->membershipPlan->plan_name }}
                                        (AED{{ $user->staff->membershipPlan->membership_fee }})
                                    @endif
                                </td>
                                <td>

                                    <form id="deleteForm{{ $user->id }}"
                                        action="{{ route('freelancerProgram.destroy', $user->id) }}" method="POST">
                                        @if ($user->freelancer_program === '0')
                                            @can('freelancer-program-edit')
                                                <a class="btn text-dark"
                                                    href="{{ route('freelancerProgram.edit', $user->id) }}?status=Accepted">
                                                    <i class="fas fa-thumbs-up"></i>
                                                </a>
                                            @endcan
                                        @elseif ($user->freelancer_program === '1')
                                            @can('freelancer-program-edit')
                                                <a class="btn text-dark"
                                                    href="{{ route('freelancerProgram.edit', $user->id) }}?status=Rejected">
                                                    <i class="fas fa-thumbs-down"></i>
                                                </a>
                                                <a class="btn text-dark"
                                                    href="{{ route('serviceStaff.edit', $user->id) }}?freelancer_join=1">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            @endcan
                                        @endif
                                        @if ($user->staff)
                                            <a class="btn text-dark"
                                                href="{{ route('serviceStaff.show', $user->id) }}?freelancer_join=1">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        @csrf
                                        @can('freelancer-program-delete')
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('{{ $user->id }}')"
                                                class="btn text-danger"><i class="fa fa-trash"></i></button>
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
        </div>
    </div>
    <script>
        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        $(document).ready(function() {
            var $input = $('#email-autocomplete');
            var $suggestions = $('#email-suggestions');
            $input.on('input', debounce(function() {
                var query = $(this).val();
                if (query.length < 2) {
                    $suggestions.hide();
                    return;
                }
                $.ajax({
                    url: '{{ route('autocomplete.email') }}',
                    data: { freelancer_program: true, query: query },
                    success: function(data) {
                        $suggestions.empty();
                        if (Array.isArray(data) && data.length) {
                            data.forEach(function(email) {
                                var $li = $('<li class="list-group-item list-group-item-action"></li>').text(email);
                                $li.on('click', function() {
                                    $input.val(email);
                                    $suggestions.hide();
                                });
                                $suggestions.append($li);
                            });
                            $suggestions.show();
                        } else {
                            var $li = $('<li class="list-group-item text-muted"></li>').text('No data found');
                            $suggestions.append($li);
                            $suggestions.show();
                        }
                    },
                    error: function(xhr) {
                        $suggestions.empty();
                        var $li = $('<li class="list-group-item text-danger"></li>').text('Error fetching data');
                        $suggestions.append($li);
                        $suggestions.show();
                    }
                });
            }, 300));
            $input.on('blur', function() {
                setTimeout(function() { $suggestions.hide(); }, 200);
            });
        });

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
