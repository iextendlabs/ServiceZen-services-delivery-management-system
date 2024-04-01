@extends('site.layout.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center">
                <h2>Complaint</h2>
            </div>
            <div class="col-md-6 d-flex justify-content-end align-items-center">
                <a class="btn btn-success" href="{{ route('siteComplaints.create') }}" title="Add new complaint"><i
                        class="fa fa-plus"></i></a>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
            </div>
        @endif
        <hr>
        <div class="row">
            <div class="col-md-9">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    @if (count($complaints))
                        @foreach ($complaints as $complaint)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $complaint->title }}</td>
                                <td>{{ $complaint->description ? substr($complaint->description, 0, 50) . '...' : '' }}</td>
                                <td>{{ $complaint->status }}</td>
                                <td>
                                    <a class="btn btn-warning" href="{{ route('siteComplaints.show', $complaint->id) }}"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no complaint.</td>
                        </tr>
                    @endif
                </table>
                {!! $complaints->links() !!}

            </div>
            <div class="col-md-3">
                <h3>Filter</h3>
                <hr>
                <form action="{{ route('siteComplaints.index') }}" method="GET" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Title:</strong>
                                <input type="text" name="title" value="{{ $filter['title'] }}" class="form-control"
                                    placeholder="Title">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Order ID:</strong>
                                <input type="number" name="order_id" value="{{ $filter['order_id'] }}" class="form-control"
                                    placeholder="Order">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <strong>Status:</strong>
                                <select name="status" class="form-control">
                                    <option></option>
                                    <option value="Open" @if ($filter['status'] === 'Open') selected @endif>Open</option>
                                    <option value="Close" @if ($filter['status'] === 'Close') selected @endif>Close</option>
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
