@extends('layouts.app')

@section('content')
    <div class="container mt-4">
        <div class="row mb-3">
            <div class="col-md-12">
                <h3 class="mb-0">Edit In App Browsing Link</h3>
            </div>
        </div>

        @if ($message = Session::get('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ $message }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Whoops!</strong> Please fix the following issues:
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('settings.appBrowsingUpdate', $setting->id) }}" method="POST" enctype="multipart/form-data" id="app-browsing-form">
            @csrf
            @method('PUT')
        
            <table class="table table-bordered" id="entries-table">
                <thead>
                    <tr>
                        <th style="width: 40%">Image</th>
                        <th style="width: 50%">Destination URL</th>
                        <th style="width: 10%">Action</th>
                    </tr>
                </thead>
                <tbody id="entries-container">
                    @if(isset($links) && count($links) > 0)
                        @foreach($links as $index => $entry)
                            <tr class="entry">
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div>
                                            @if(isset($entry['image']) && file_exists(public_path('app-browsing-icon/'.$entry['image'])))
                                                <input type="hidden" name="entries[{{ $index }}][existing_image]" value="{{ $entry['image'] }}">
                                            @endif
                                            <input type="file"
                                                   class="form-control image-upload"
                                                   name="entries[{{ $index }}][image]"
                                                   accept="image/*"
                                                   style="width: 220px;">
                                            <small class="form-text text-muted">300x300px</small>
                                        </div>
                                        <div class="image-preview" style="{{ isset($entry['image']) ? '' : 'display:none;' }}">
                                            <img src="{{ isset($entry['image']) ? asset('app-browsing-icon/'.$entry['image']) : '#' }}"
                                                 alt="Image preview"
                                                 style="max-height: 80px; width: auto;"
                                                 class="img-thumbnail">
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <input type="url" class="form-control" name="entries[{{ $index }}][destination_url]" placeholder="https://example.com" value="{{ $entry['destinationUrl'] ?? '' }}">
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-danger remove-entry"><i class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr class="entry">
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div>
                                        <input type="file"
                                               class="form-control image-upload"
                                               name="entries[0][image]"
                                               accept="image/*"
                                               style="width: 220px;">
                                        <small class="form-text text-muted">300x300px</small>
                                    </div>
                                    <div class="image-preview" style="display:none;">
                                        <img src="#"
                                             alt="Image preview"
                                             style="max-height: 80px; width: auto;"
                                             class="img-thumbnail">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <input type="url" class="form-control" name="entries[0][destination_url]" placeholder="https://example.com">
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-entry"><i class="fas fa-trash"></i></button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
            
        
            <div class="d-flex justify-content-between mt-4">
                <button type="button" id="add-entry" class="btn btn-success">
                    <i class="fas fa-plus"></i> Add New Entry
                </button>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save"></i> Update
                </button>
            </div>
        </form>
    </div>

    <script>
        let entryIndex = {{ isset($links) && count($links) > 0 ? count($links) : 1 }};
    
        $('#add-entry').on('click', function () {
            const html = `
                <tr class="entry">
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div>
                                <input type="file" class="form-control image-upload" name="entries[${entryIndex}][image]" accept="image/*" style="width: 220px;">
                                <small class="form-text text-muted">300x300px</small>
                            </div>
                            <div class="image-preview" style="display:none;">
                                <img src="#" alt="Image preview" style="max-height: 80px; width: auto;" class="img-thumbnail">
                            </div>
                        </div>
                    </td>
                    <td>
                        <input type="url" class="form-control" name="entries[${entryIndex}][destination_url]" placeholder="https://example.com">
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-danger remove-entry"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>`;
            $('#entries-container').append(html);
            entryIndex++;
        });
    
        // Remove entry
        $(document).on('click', '.remove-entry', function () {
            $(this).closest('tr').remove();
        });
    
        // Preview image and replace existing preview
        $(document).on('change', '.image-upload', function () {
            const input = this;
            const previewWrapper = $(this).closest('td').find('.image-preview');
            const previewImg = previewWrapper.find('img');
    
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImg.attr('src', e.target.result);
                    previewWrapper.show();
                };
                reader.readAsDataURL(input.files[0]);
            } else {
                previewWrapper.hide();
            }
        });
    </script>    
    
@endsection