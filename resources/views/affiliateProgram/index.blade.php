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
            <div class="col-md-12">
            <form action="{{ route('affiliateProgram.index') }}" method="GET" enctype="multipart/form-data">
                <div class="form-row">

                <!-- Status -->
                <div class="form-group col-md-3">
                    <label class="font-weight-bold">Status</label>
                    <select name="status" class="form-control">
                    <option value="">-- Select Status --</option>
                    <option value="2" @if ($filter_status === '2') selected @endif>New</option>
                    <option value="1" @if ($filter_status === '1') selected @endif>Accepted</option>
                    <option value="0" @if ($filter_status === '0') selected @endif>Rejected</option>
                    </select>
                </div>

                <!-- Name -->
                <div class="form-group col-md-3">
                    <label class="font-weight-bold">Name</label>
                    <input type="text" name="name" value="{{ $filter_name }}" class="form-control" placeholder="Enter Name">
                </div>

                <!-- Email -->
                <div class="form-group col-md-3 position-relative">
                    <label class="font-weight-bold">Email</label>
                    <input type="email" name="email" id="email-autocomplete" value="{{ $filter_email }}" class="form-control" placeholder="Enter Email" autocomplete="off">
                    <ul id="email-suggestions" class="list-group position-absolute w-100" style="z-index:1000; display:none; max-height:180px; overflow-y:auto;"></ul>
                </div>

                <!-- Button -->
                <div class="form-group col-md-3 text-right mt-4">
                    <button type="submit" class="btn btn-primary px-4 w-100 mt-2">
                    <i class="fa fa-search mr-1"></i> Filter
                    </button>
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
                                    @if ($user->affiliate_program === '1')
                                        <span class="badge bg-primary">Accepted</span>
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
                                            <a class="btn text-dark"
                                                href="{{ route('affiliateProgram.edit', $user->id) }}?status=Accepted">
                                                <i class="fas fa-thumbs-up"></i>
                                            </a>
                                            @endcan
                                        @elseif ($user->affiliate_program === '1')
                                            @can('affiliate-program-edit')
                                            <a class="btn text-dark"
                                                href="{{ route('affiliateProgram.edit', $user->id) }}?status=Rejected">
                                                <i class="fas fa-thumbs-down"></i>
                                            </a>
                                            <a class="btn text-dark" href="{{ route('affiliates.edit', $user->id) }}">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endcan
                                            <a class="btn text-dark" href="{{ route('affiliates.show', $user->id) }}">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif

                                        @csrf
                                        @method('DELETE')
                                        @can('affiliate-program-delete')
                                        <button type="button" class="btn text-danger"
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
                    data: { affiliate_program: true, query: query },
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
