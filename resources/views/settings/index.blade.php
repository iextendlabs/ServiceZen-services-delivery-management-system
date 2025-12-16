@extends('layouts.app')
@section('content')
@section('page_title')
<h3 class="">Settings</h3>
@endsection
    <div class="container-fluid px-1">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Setting ({{ $total_setting }})</h2>
                </div>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success">
                <span>{{ $message }}</span>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card mt-3">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead>
                            <tr>
                                <th>Sr#</th>
                                <th>
                                    <i><a class="ml-2 text-dark" href="{{ route('settings.index', array_merge(request()->query(), ['sort' => 'key', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Key</a></i>
                                    @if (request('sort') === 'key')
                                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                                    @endif
                                </th>
                                <th>
                                    <i><a class="ml-2 text-dark" href="{{ route('settings.index', array_merge(request()->query(), ['sort' => 'value', 'direction' => request('direction', 'asc') == 'asc' ? 'desc' : 'asc'])) }}">Value</a></i>
                                    @if (request('sort') === 'value')
                                        <i class="fa {{ $direction == 'asc' ? 'fa-arrow-down' : 'fa-arrow-up' }} px-2 py-2"></i>
                                    @endif
                                </th>
                                <th width="280px">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($settings))
                                @foreach ($settings as $setting)
                                    <tr>
                                        <td>{{ ++$i }}</td>
                                        <td>{{ $setting->key }}</td>
                                        <td>{{ substr($setting->value, 0, 50) }}</td>
                                        <td>
                                            @can('setting-edit')
                                                <a class="btn text-dark" href="{{ route('settings.edit', $setting->id) }}"><i class="fa fa-edit"></i></a>
                                            @endcan
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="5" class="text-center">There is no Setting.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="mt-3">
            {!! $settings->links() !!}
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
