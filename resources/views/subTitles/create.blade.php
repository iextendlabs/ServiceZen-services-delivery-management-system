@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 margin-tb">
                <h2>Add New Sub Title / Designation</h2>
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
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <span style="color: red;">*</span><strong>Name:</strong>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control"
                            placeholder="Name">
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <strong>Parent Subtitle:</strong>
                        <select name="parent_id" class="form-control select2">
                            <option value="">-- None --</option>
                            @foreach ($allSubTitles as $subtitle)
                                <option value="{{ $subtitle->id }}"
                                    {{ old('parent_id') == $subtitle->id ? 'selected' : '' }}>{{ $subtitle->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group scroll-div">
                        <strong>Child Subtitles:</strong>
                        <input type="text" id="childSubtitleSearch" class="form-control mb-2"
                            placeholder="Search Subtitles">
                        <table class="table table-bordered" id="childSubtitlesTable">
                            <thead>
                                <tr>
                                    <th>Select</th>
                                    <th>Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($allSubTitles as $subtitle)
                                    <tr>
                                        <td><input type="checkbox" name="child_subtitles[]" value="{{ $subtitle->id }}"
                                                {{ is_array(old('child_subtitles')) && in_array($subtitle->id, old('child_subtitles', [])) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $subtitle->name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
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
