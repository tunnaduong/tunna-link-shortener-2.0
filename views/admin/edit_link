@extends('admin.layout')

@section('content')
    <div class="edit-link-page">
        <div class="page-header">
            <h2>Edit Link: {{ $link->getCode() }}</h2>
            <a href="/admin/links" class="btn btn-secondary">Back to Links</a>
        </div>

        <div class="edit-link-form-container">
            <form method="POST" action="/admin/edit-link?code={{ urlencode($link->getCode()) }}" class="edit-link-form"
                enctype="multipart/form-data">
                <div class="form-section">
                    <h3>Basic Information</h3>

                    <div class="form-group">
                        <label for="next_url">Destination URL *</label>
                        <input type="url" id="next_url" name="next_url" value="{{ $link->getNextUrl() }}" required>
                        <small>Enter the URL you want to redirect to</small>
                    </div>

                    <div class="form-group">
                        <label for="link_title">Link Title</label>
                        <input type="text" id="link_title" name="link_title" value="{{ $link->getLinkTitle() ?? '' }}">
                        <small>Optional title for the link</small>
                    </div>

                    <div class="form-group">
                        <label for="link_excerpt">Link Description</label>
                        <textarea id="link_excerpt" name="link_excerpt" rows="3">{{ $link->getLinkExcerpt() ?? '' }}</textarea>
                        <small>Optional description for the link</small>
                    </div>

                    <div class="form-group">
                        <label for="link_preview_url">Preview Image</label>
                        <div class="image-upload-container">
                            <input type="file" id="preview_image_file" name="preview_image_file" accept="image/*"
                                class="image-upload-input">
                            <div class="image-upload-preview" id="preview-image-preview">
                                @if ($link->getLinkPreviewUrl())
                                    <img src="{{ $link->getLinkPreviewUrl() }}"
                                        style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                                @else
                                    <span class="upload-text">Click to upload preview image or drag & drop</span>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" id="link_preview_url" name="link_preview_url"
                            value="{{ $link->getLinkPreviewUrl() ?? '' }}">
                        <small>Upload an image that will be shown as preview</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Redirect Settings</h3>

                    <div class="form-group">
                        <label for="redirect_type">Redirect Type</label>
                        <select id="redirect_type" name="redirect_type">
                            <option value="0" {{ $link->getRedirectType() == 0 ? 'selected' : '' }}>Direct Redirect
                            </option>
                            <option value="1" {{ $link->getRedirectType() == 1 ? 'selected' : '' }}>Click Through
                            </option>
                            <option value="2" {{ $link->getRedirectType() == 2 ? 'selected' : '' }}>reCAPTCHA Protected
                            </option>
                            <option value="3" {{ $link->getRedirectType() == 3 ? 'selected' : '' }}>Password Protected
                            </option>
                            <option value="4" {{ $link->getRedirectType() == 4 ? 'selected' : '' }}>Instant Redirect
                                (No Ads)</option>
                        </select>
                    </div>

                    <div class="form-group" id="password-field"
                        style="{{ $link->getRedirectType() == 3 ? '' : 'display: none;' }}">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" value="{{ $link->getPassword() ?? '' }}">
                        <small>Required for password protected links</small>
                    </div>

                    <div class="form-group">
                        <label for="wait_seconds">Wait Time (seconds)</label>
                        <input type="number" id="wait_seconds" name="wait_seconds" value="{{ $link->getWaitSeconds() }}"
                            min="0" max="60">
                        <small>How long to wait before redirect (0-60 seconds)</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Advertisement Settings</h3>

                    <div class="form-group">
                        <label for="ads_img_url">Advertisement Image</label>
                        <div class="image-upload-container">
                            <input type="file" id="ads_image_file" name="ads_image_file" accept="image/*"
                                class="image-upload-input">
                            <div class="image-upload-preview" id="image-preview">
                                @if (!empty($link->getAdsImgUrl()))
                                    <img src="{{ $link->getAdsImgUrl() }}"
                                        style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                                @else
                                    <span class="upload-text">Click to upload image or drag & drop</span>
                                @endif
                            </div>
                        </div>
                        <input type="hidden" id="ads_img_url" name="ads_img_url"
                            value="{{ !empty($link->getAdsImgUrl()) ? $link->getAdsImgUrl() : '' }}">
                        <small>Upload an image for advertisement display</small>
                    </div>

                    <div class="form-group">
                        <label for="ads_click_url">Ad Click URL</label>
                        <input type="url" id="ads_click_url" name="ads_click_url"
                            value="{{ !empty($link->getAdsClickUrl()) ? $link->getAdsClickUrl() : '' }}">
                        <small>URL to redirect to when ad is clicked</small>
                    </div>

                    <div class="form-group">
                        <label for="ads_promoted_by">Promoted By</label>
                        <input type="text" id="ads_promoted_by" name="ads_promoted_by"
                            value="{{ !empty($link->getAdsPromotedBy()) ? $link->getAdsPromotedBy() : '' }}">
                        <small>Who is promoting this ad</small>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Additional Settings</h3>

                    <div class="form-group">
                        <label for="tag">Tag</label>
                        <input type="text" id="tag" name="tag" value="{{ $link->getTag() ?? '' }}">
                        <small>Optional tag for categorization</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn">Update Link</button>
                    <a href="/admin/links" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Password field toggle
        document.getElementById("redirect_type").addEventListener("change", function() {
            const passwordField = document.getElementById("password-field");
            if (this.value == "3") {
                passwordField.style.display = "block";
            } else {
                passwordField.style.display = "none";
            }
        });

        // Image upload functionality
        const imageUploadInput = document.getElementById("ads_image_file");
        const imagePreview = document.getElementById("image-preview");
        const adsImgUrlInput = document.getElementById("ads_img_url");

        imageUploadInput.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML =
                        `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
                    // Don\'t set the hidden input for file uploads - let the server handle it
                };
                reader.readAsDataURL(file);
            }
        });

        // Drag and drop functionality
        imagePreview.addEventListener("dragover", function(e) {
            e.preventDefault();
            imagePreview.style.backgroundColor = "#f0f0f0";
        });

        imagePreview.addEventListener("dragleave", function(e) {
            e.preventDefault();
            imagePreview.style.backgroundColor = "";
        });

        imagePreview.addEventListener("drop", function(e) {
            e.preventDefault();
            imagePreview.style.backgroundColor = "";

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageUploadInput.files = files;
                imageUploadInput.dispatchEvent(new Event("change"));
            }
        });

        // Preview image upload functionality
        const previewImageUploadInput = document.getElementById("preview_image_file");
        const previewImagePreview = document.getElementById("preview-image-preview");
        const linkPreviewUrlInput = document.getElementById("link_preview_url");

        previewImageUploadInput.addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImagePreview.innerHTML =
                        `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
                    // Don\'t set the hidden input for file uploads - let the server handle it
                };
                reader.readAsDataURL(file);
            }
        });

        // Preview image drag and drop functionality
        previewImagePreview.addEventListener("dragover", function(e) {
            e.preventDefault();
            previewImagePreview.style.backgroundColor = "#f0f0f0";
        });

        previewImagePreview.addEventListener("dragleave", function(e) {
            e.preventDefault();
            previewImagePreview.style.backgroundColor = "";
        });

        previewImagePreview.addEventListener("drop", function(e) {
            e.preventDefault();
            previewImagePreview.style.backgroundColor = "";

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                previewImageUploadInput.files = files;
                previewImageUploadInput.dispatchEvent(new Event("change"));
            }
        });
    </script>
@endsection
