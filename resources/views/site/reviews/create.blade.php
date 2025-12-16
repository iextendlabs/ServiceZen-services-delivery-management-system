<div id="reviewFormErrors" class="alert alert-danger alert-dismissible fade" role="alert" style="display:none; margin-bottom: 1.5rem;">
    <strong>Please fix the errors below:</strong>
    <ul id="errorList" class="mb-0 mt-2"></ul>
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>

<form id="reviewForm" action="{{ route('siteReviews.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if(!Request::is('/'))

        @if(isset($service->id))
            <input type="hidden" name="service_id" value="{{ $service->id }}">
        @endif
        @if(isset($user->id))
            <input type="hidden" name="staff_id" value="{{ $user->id }}">
        @endif

        @if(isset($order->service_staff_id))
            <input type="hidden" name="staff_id" value="{{ $order->service_staff_id }}">
            <input type="hidden" name="order_id" value="{{ $order->id }}">
        @endif


    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Your Name:</strong>
                <input type="text" name="user_name" value="{{ old('user_name') }}" class="form-control" required>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <span style="color: red;">*</span><strong>Review:</strong>
                <textarea class="form-control" style="height:150px" name="content" placeholder="Review">{{old('content')}}</textarea>
            </div>
        </div>
        <!-- Upload Images Section -->
        <div class="col-md-12">
            <div class="form-group">
                <label style="font-weight: 600; color: #1f2937; margin-bottom: 12px; display: block;">
                    <i class="fa fa-image" style="color: #6b21a8; margin-right: 8px;"></i>Upload Images
                </label>
                
                <!-- Images Container -->
                <div id="imagesContainer" style="margin-bottom: 16px;">
                    <!-- Images will be added here dynamically -->
                </div>

                <!-- Add Image Button -->
                <button id="addImageBtn" type="button" class="btn" style="background-color: #f3f4f6; color: #6b21a8; border: 2px dashed #6b21a8; padding: 12px 20px; border-radius: 8px; font-weight: 600; transition: all 0.3s ease; cursor: pointer;">
                    <i class="fa fa-plus" style="margin-right: 6px;"></i>Add Image
                </button>
            </div>
        </div>

        <!-- Upload Video Section -->
        <div class="col-md-12">
            <div class="form-group">
                <label style="font-weight: 600; color: #1f2937; margin-bottom: 12px; display: block;">
                    <i class="fa fa-video-camera" style="color: #6b21a8; margin-right: 8px;"></i>Upload Video
                </label>
                
                <div style="display: flex; align-items: center; gap: 12px;">
                    <!-- Hidden file input -->
                    <input type="file" id="videoUpload" name="video" accept="video/*" style="display: none;">
                    
                    <!-- Custom upload button -->
                    <label for="videoUpload" style="cursor: pointer; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; background-color: #f3f4f6; border: 2px dashed #6b21a8; border-radius: 8px; transition: all 0.3s ease;">
                        <i class="fa fa-video-camera" style="color: #6b21a8; font-size: 20px;"></i>
                    </label>
                    
                    <!-- File name display -->
                    <span id="videoFileName" style="font-size: 14px; color: #6b7280;">No file chosen</span>
                </div>

                <!-- Video Preview -->
                <video id="videoPreview" controls style="display: none; max-width: 100%; margin-top: 12px; border-radius: 8px;"></video>
            </div>
        </div>
        <!-- Rating Section -->
        <div class="col-md-12">
            <div class="form-group">
                <label style="font-weight: 600; color: #1f2937; margin-bottom: 16px; display: block;">
                    <span style="color: red;">*</span> How would you rate your experience?
                </label>
                
                <!-- Star Rating Container -->
                <div class="star-rating-container" style="display: flex; justify-content: center; gap: 8px; margin-bottom: 8px;">
                    @for($i = 1; $i <= 5; $i++)
                        <input 
                            type="radio" 
                            id="rating{{ $i }}" 
                            name="rating" 
                            value="{{ $i }}" 
                            {{ old('rating') == $i ? 'checked' : '' }}
                            style="display: none;"
                        >
                        <label 
                            for="rating{{ $i }}" 
                            class="star-label"
                            style="
                                font-size: 40px;
                                color: #d1d5db;
                                cursor: pointer;
                                transition: all 0.2s ease;
                                text-shadow: 0 2px 4px rgba(0,0,0,0.1);
                            "
                            data-rating="{{ $i }}"
                        >★</label>
                    @endfor
                </div>
                
                <!-- Rating Text Display -->
                <div style="text-align: center; color: #6b7280; font-size: 14px; font-weight: 500; margin-top: 8px;">
                    <span id="ratingText">Select your rating</span>
                </div>
            </div>
        </div>
        <div class="col-md-12 text-right">
            <button type="submit" class="btn" style="background: linear-gradient(45deg, #6b21a8, #7c3aed); color: white; font-weight: 600; padding: 10px 24px; border: none; border-radius: 6px;">Submit Review</button>
        </div>
    </div>
</form>
<script>
    $(document).ready(function() {
        // Rating stars interactive functionality
        const ratingLabels = document.querySelectorAll('.star-label');
        const ratingText = document.getElementById('ratingText');
        const ratingTexts = {
            1: '😞 Poor',
            2: '😕 Fair',
            3: '😐 Good',
            4: '😊 Very Good',
            5: '🤩 Excellent!'
        };

        ratingLabels.forEach((label, index) => {
            label.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                updateStars(rating);
                ratingText.textContent = ratingTexts[rating];
                ratingText.style.color = '#6b21a8';
            });

            label.addEventListener('click', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                document.getElementById('rating' + rating).checked = true;
                ratingText.textContent = ratingTexts[rating];
                ratingText.style.color = '#6b21a8';
                ratingText.style.fontWeight = '600';
            });
        });

        // Reset stars on mouse leave
        document.querySelector('.star-rating-container').addEventListener('mouseleave', function() {
            const checkedRating = document.querySelector('input[name="rating"]:checked');
            if (checkedRating) {
                updateStars(parseInt(checkedRating.value));
                ratingText.textContent = ratingTexts[parseInt(checkedRating.value)];
            } else {
                updateStars(0);
                ratingText.textContent = 'Select your rating';
                ratingText.style.color = '#6b7280';
                ratingText.style.fontWeight = '500';
            }
        });

        // Update stars visual based on rating
        function updateStars(rating) {
            ratingLabels.forEach((label, index) => {
                const starIndex = index + 1;
                if (starIndex <= rating) {
                    label.style.color = '#fbbf24';
                    label.style.transform = 'scale(1.1)';
                    label.style.textShadow = '0 4px 12px rgba(251, 191, 36, 0.4)';
                } else {
                    label.style.color = '#d1d5db';
                    label.style.transform = 'scale(1)';
                    label.style.textShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
                }
            });
        }

        // Initialize stars on page load
        window.addEventListener('load', function() {
            const checkedRating = document.querySelector('input[name="rating"]:checked');
            if (checkedRating) {
                updateStars(parseInt(checkedRating.value));
                ratingText.textContent = ratingTexts[parseInt(checkedRating.value)];
                ratingText.style.fontWeight = '600';
            }
        });

        // Add Image functionality
        $("#addImageBtn").click(function() {
            var imageId = 'img-' + Date.now();
            var imageHtml = `
                <div class="image-item" style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px; padding: 12px; background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                    <!-- Hidden file input -->
                    <input type="file" id="` + imageId + `" name="images[]" class="form-control image-input" accept="image/*" style="display: none;">
                    
                    <!-- Custom upload button -->
                    <label for="` + imageId + `" style="cursor: pointer; display: flex; align-items: center; justify-content: center; width: 48px; height: 48px; background-color: #fff; border: 2px dashed #6b21a8; border-radius: 8px; flex-shrink: 0; transition: all 0.3s ease;">
                        <i class="fa fa-image" style="color: #6b21a8; font-size: 20px;"></i>
                    </label>
                    
                    <!-- Image preview -->
                    <img class="image-preview" style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover; display: none; flex-shrink: 0;">
                    
                    <!-- File name -->
                    <span class="image-name" style="font-size: 13px; color: #6b7280; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">No file chosen</span>
                    
                    <!-- Remove button -->
                    <button type="button" class="btn remove-image" style="background-color: #fee2e2; color: #dc2626; border: none; padding: 6px 12px; border-radius: 6px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all 0.3s ease;">
                        <i class="fa fa-trash" style="margin-right: 4px;"></i>Remove
                    </button>
                </div>
            `;
            $("#imagesContainer").append(imageHtml);
        });

        // Handle image file selection
        $(document).on("change", ".image-input", function(e) {
            var file = this.files[0];
            var $item = $(this).closest('.image-item');
            var $preview = $item.find('.image-preview');
            var $name = $item.find('.image-name');
            
            if (file) {
                $name.text(file.name);
                var reader = new FileReader();
                reader.onload = function(event) {
                    $preview.attr('src', event.target.result).show();
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove image
        $(document).on("click", ".remove-image", function() {
            $(this).closest('.image-item').remove();
        });

        // Video upload
        $('#videoUpload').on('change', function() {
            var file = this.files[0];
            var $videoPreview = $('#videoPreview');
            var $videoFileName = $('#videoFileName');
            
            if (file) {
                $videoFileName.text(file.name);
                var reader = new FileReader();
                reader.onload = function(event) {
                    $videoPreview.attr('src', event.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                $videoFileName.text('No file chosen');
                $videoPreview.attr('src', '').hide();
            }
        });
    });
</script>