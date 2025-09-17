@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12 margin-tb">
            <h2>Add New Service Staff</h2>
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
    <form action="{{ route('serviceStaff.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="tab-content" id="myTabsContent">
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Name:</strong>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Name">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Email:</strong>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="abc@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Sub Title / Designation</strong>
                            <select class="form-control selectpicker" id="sub_titles" name="sub_titles[]"
                                multiple data-live-search="true" data-actions-box="true">
                                @foreach ($subTitles as $subTitle)
                                    <option value="{{ $subTitle->id }}" {{ in_array($subTitle->id, old('sub_titles', [])) ? 'selected' : '' }}>{{ $subTitle->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Phone Number:</strong>
                            <input id="number_country_code" type="hidden" name="number_country_code" />
                            <input type="tel" id="number" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Whatsapp Number:</strong>
                            <input id="whatsapp_country_code" type="hidden" name="whatsapp_country_code" />
                            <input type="tel" id="whatsapp" name="whatsapp" class="form-control" value="{{ old('whatsapp') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Status:</strong>
                            <select name="status" class="form-control">
                                <option value="1"  {{ old('status') == '1' ? 'selected' : '' }}>Enable</option>
                                <option value="0"  {{ old('status') == '0' ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <strong>Sort Order:</strong>
                            <input type="number" name="sort" class="form-control"
                                value="{{ old('sort') }}">
                            <small class="text-muted">Lower numbers appear first</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                                <input type="hidden" name="feature" value="0">

                                <input class="form-check-input" type="checkbox" name="feature" id="feature" value="1"
                                    {{ old('feature') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="feature"><strong>Enable featured staff On Web:</strong></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="form-check form-switch">
                                <!-- Hidden field ensures a value is sent when checkbox is unchecked -->
                                <input type="hidden" name="feature_on_app" value="0">

                                <input class="form-check-input" type="checkbox" name="feature_on_app" id="feature_on_app" value="1"
                                    {{ old('feature_on_app') == '1' ? 'checked' : '' }}>
                                <label class="form-check-label" for="feature_on_app"><strong>Enable featured staff On App:</strong></label>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Online:</strong>
                                        <select name="online" class="form-control">
                                            <option value="1"  {{ old('online') == '1' ? 'selected' : '' }}>Online</option>
                                            <option value="0"  {{ old('online') == '0' ? 'selected' : '' }}>Offline</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Get Quote:</strong>
                                        <select name="get_quote" class="form-control">
                                            <option value="1"  {{ old('get_quote') == '1' ? 'selected' : '' }}>Enable</option>
                                            <option value="0"  {{ old('get_quote') == '0' ? 'selected' : '' }}>Disable</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Show Quote Detail:</strong>
                                        <select name="show_quote_detail" class="form-control">
                                            <option value="1"  {{ old('show_quote_detail') == '1' ? 'selected' : '' }}>Enable</option>
                                            <option value="0"  {{ old('show_quote_detail') == '0' ? 'selected' : '' }}>Disable</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Quote Amount:</strong>
                                        <input type="number" step="0.01" name="quote_amount" class="form-control" value="{{ old('quote_amount') }}" placeholder="Quote Amount">
                                        <small class="form-text text-muted">Minimum value: 0.01</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <strong>Quote Commission:</strong>
                                        <input type="number" name="quote_commission" class="form-control" value="{{ old('quote_commission') }}" placeholder="Quote Commission In %">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                            <strong for="image">Upload Image</strong>
                            <p class="text-danger"><strong>Note: </strong>Upload image with dimensions 318 x 256px Thank you!</p>
                            <input type="file" name="image" class="form-control image-input" accept="image/*">
                            <img class="image-preview" height="130px" src="/staff-images/default.png">
                        </div>
                    </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <strong>About:</strong>
                            <textarea name="about" id="summernote" class="form-control">{{ old('about')}}</textarea>
                            <script>
                                (function($) {
                                    $('#summernote').summernote({
                                        tabsize: 2,
                                        height: 250,
                                        toolbar: [
                                            ['style', ['style']],
                                            ['font', ['bold', 'italic', 'underline', 'clear']],
                                            ['fontname', ['fontname']],
                                            ['fontsize', ['fontsize']],
                                            ['color', ['color']],
                                            ['para', ['ul', 'ol', 'paragraph']],
                                            ['height', ['height']],
                                            ['insert', ['picture', 'link', 'video', 'table']],
                                            ['misc', ['undo', 'redo']], 
                                            ['view', ['fullscreen', 'codeview', 'help']]
                                        ],
                                        popover: {
                                            image: [
                                                ['custom', ['imageAttributes']],
                                                ['resize', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                                                ['float', ['floatLeft', 'floatRight', 'floatNone']],
                                                ['remove', ['removeMedia']]
                                            ]
                                        },
                                        callbacks: {
                                            onImageUpload: function(files) {
                                                uploadImage(files[0]);
                                            }
                                        }
                                    });
    
                                    function uploadImage(file) {
                                        let data = new FormData();
                                        data.append("file", file);
                                        data.append("_token", "{{ csrf_token() }}");
    
                                        $.ajax({
                                            url: "{{ route('summerNote.upload') }}",
                                            method: "POST",
                                            data: data,
                                            processData: false,
                                            contentType: false,
                                            success: function(response) {
                                                $('#summernote').summernote('insertImage', response.url);
                                            },
                                            error: function(response) {
                                                console.error(response);
                                            }
                                        });
                                    }
                                })(jQuery);
                            </script>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Password:</strong>
                            <input type="password" name="password" class="form-control" placeholder="Password">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <span style="color: red;">*</span><strong>Confirm Password:</strong>
                            <input type="password" name="confirm-password" class="form-control" placeholder="Confirm Password">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group scroll-div">
                            <strong>Supervisor:</strong>
                            <input type="text" name="search-supervisor" id="search-supervisor" class="form-control" placeholder="Search Supervisor By Name And Email">
                            <table class="table table-striped table-bordered supervisor-table">
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                </tr>
                                @foreach ($supervisors as $user)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="ids[]" value="{{ $user->id }}" {{ in_array($user->id, old('ids', [])) ? 'checked' : '' }}>
                                    </td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Location:</strong>
                            <input type="text" name="location" class="form-control" placeholder="Location" value="{{ old('location') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Nationality:</strong>
                            <input type="text" name="nationality" class="form-control" placeholder="Nationality" value="{{ old('nationality') }}">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Delivered Order:</strong>
                            <input type="number" name="delivered_order" value="{{ old('delivered_order') }}" class="form-control" placeholder="Delivered Order">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Additional Charges:</strong>
                            <input type="number" name="charges" class="form-control" value="{{ old('charges') }}" placeholder="Additional Charges">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Commission Salary:</strong>
                            <input type="number" name="fix_salary" class="form-control" value="{{ old('fix_salary') }}" placeholder="Commission Salary">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <strong>Minimum Order Value:</strong>
                            <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value') }}" placeholder="Minmum Order Value">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center mt-3">
                <button type="submit" class="btn btn-block btn-primary">Save</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(document).ready(function() {
        $("#search-supervisor").keyup(function() {
            var value = $(this).val().toLowerCase();

            $(".supervisor-table tr").hide();

            $(".supervisor-table tr").each(function() {

                $row = $(this);

                var name = $row.find("td:first").next().text().toLowerCase();

                var email = $row.find("td:last").text().toLowerCase();

                if (name.indexOf(value) != -1) {
                    $(this).show();
                } else if (email.indexOf(value) != -1) {
                    $(this).show();
                }
            });
        });
    });
</script>
@endsection