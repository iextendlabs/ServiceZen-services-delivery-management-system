@extends('layouts.app')
@section('content')
    <div class="container-fluid px-1">
        <div class="row">
            <div class="col-12">
                <div class="d-flex align-items-center justify-content-between">
                    <h2 class="mb-0">Add New Sub Title / Designation</h2>
                </div>
            </div>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> There were some problems with your input.<br><br>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('subTitles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="card mt-3">
                <div class="card-body">
                    <div class="form-row">
                        <div class="form-group col-12 col-md-6 mb-3">
                            <label class="font-weight-bold"><span class="text-danger">*</span> Name</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Name">
                        </div>

                        <div class="form-group col-12 col-md-6 mb-3">
                            <label class="font-weight-bold">Parent Subtitle</label>
                            <select name="parent_id" class="form-control select2">
                                <option value="">-- None --</option>
                                @foreach ($allSubTitles as $subtitle)
                                    <option value="{{ $subtitle->id }}" {{ old('parent_id') == $subtitle->id ? 'selected' : '' }}>{{ $subtitle->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-12 mb-3 scroll-div">
                            <label class="font-weight-bold">Child Subtitles</label>
                            <input type="text" id="childSubtitleSearch" class="form-control mb-2" placeholder="Search Subtitles">
                            <div class="table-responsive">
                                <table class="table table-bordered mb-0" id="childSubtitlesTable">
                                    <thead>
                                        <tr>
                                            <th style="width:80px">Select</th>
                                            <th>Name</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($allSubTitles as $subtitle)
                                            <tr>
                                                <td class="align-middle"><input type="checkbox" name="child_subtitles[]" value="{{ $subtitle->id }}" {{ is_array(old('child_subtitles')) && in_array($subtitle->id, old('child_subtitles', [])) ? 'checked' : '' }}></td>
                                                <td class="align-middle">{{ $subtitle->name }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 text-center mt-2">
                            <button type="submit" class="btn btn-md btn-primary shadow-sm float-end font-weight-bold">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <script>
        $(document).ready(function() {
            $('#childSubtitleSearch').on('keyup', function() {
                var value = $(this).val().toLowerCase();
                $('#childSubtitlesTable tbody tr').filter(function() {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });

            var $parentSelect = $('select[name="parent_id"]');
            $parentSelect.on('change', function() {
                var parentId = $(this).val();
                $('#childSubtitlesTable tbody tr').each(function() {
                    var checkbox = $(this).find('input[name="child_subtitles[]"]');
                    if (checkbox.val() == parentId) {
                        checkbox.prop('checked', false);
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
                updateSelect2Options();
            });

            $('#childSubtitlesTable').on('change', 'input[name="child_subtitles[]"]', function() {
                var checkedIds = [];
                $('#childSubtitlesTable tbody input[name="child_subtitles[]"]:checked').each(function() {
                    checkedIds.push($(this).val());
                });
                updateSelect2Options();
            });

            function updateSelect2Options() {
                var checkedIds = [];
                $('#childSubtitlesTable tbody input[name="child_subtitles[]"]:checked').each(function() {
                    checkedIds.push($(this).val());
                });
                var parentId = $parentSelect.val();
                $parentSelect.find('option').each(function() {
                    var val = $(this).val();
                    if (checkedIds.includes(val)) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
                if (checkedIds.includes(parentId)) {
                    $parentSelect.val('').trigger('change.select2');
                } else {
                    $parentSelect.trigger('change.select2');
                }
            }
        });
    </script>
@endsection
