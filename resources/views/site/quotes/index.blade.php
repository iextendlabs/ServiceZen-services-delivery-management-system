@extends('site.layout.app')
@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 d-flex align-items-center">
                <h2>Quotes</h2>
            </div>
        </div>

        <hr>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered">
                    <tr>
                        <th>Sr#</th>
                        <th>Service</th>
                        <th>Detail</th>
                        <th>Action</th>
                    </tr>
                    @if (count($quotes))
                        @foreach ($quotes as $quote)
                            <tr>
                                <td>{{ ++$i }}</td>
                                <td>{{ $quote->service_name }}</td>
                                <td>{{ $quote->detail ? substr($quote->detail, 0, 50) . '...' : '' }}</td>
                                <td>
                                    <a class="btn btn-warning" href="{{ route('siteQuotes.show', $quote->id) }}"><i class="fa fa-eye"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="text-center">There is no quote.</td>
                        </tr>
                    @endif
                </table>
                {!! $quotes->links() !!}

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
