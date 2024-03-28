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
                        <th>Action</th>
                    </tr>
                    @if (count($users))
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->affiliate_program === '1' ? 'Accepted' : 'Rejected' }}</td>
                                <td>
                                    @if($user->affiliate_program === '0')
                                    <a class="btn btn-sm btn-success" href="{{ route('affiliateProgram.edit', $user->id) }}?status=Accepted">
                                        <i class="fas fa-thumbs-up"></i>
                                    </a>
                                    @elseif ($user->affiliate_program === '1')
                                    <a class="btn btn-sm btn-danger" href="{{ route('affiliateProgram.edit', $user->id) }}?status=Rejected">
                                        <i class="fas fa-thumbs-down"></i>
                                    </a>
                                    <a class="btn btn-sm btn-primary" href="{{ route('affiliates.edit', $user->id) }}">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a class="btn btn-sm btn-warning" href="{{ route('affiliates.show', $user->id) }}">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endif
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
                                    <option value="1" @if ($filter_status === '1') selected @endif>Accepted</option>
                                    <option value="0" @if ($filter_status === '0') selected @endif>Rejected</option>
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
