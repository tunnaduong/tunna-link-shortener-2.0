<?php
$content = '
<div class="create-link-page">
    <div class="page-header">
        <h2>Create New Link</h2>
        <a href="/admin/links" class="btn btn-secondary">Back to Links</a>
    </div>
    
    <div class="create-link-form-container">
        <form method="POST" action="/admin/create-link" class="create-link-form" enctype="multipart/form-data">
            <div class="form-section">
                <h3>Basic Information</h3>
                
                <div class="form-group">
                    <label for="next_url">Destination URL *</label>
                    <div class="url-input-group">
                        <input type="url" id="next_url" name="next_url" required 
                               placeholder="https://example.com" 
                               value="' . htmlspecialchars($_POST['next_url'] ?? '') . '">
                        <button type="button" id="extract-og-btn" class="btn btn-small">Extract Open Graph</button>
                    </div>
                    <small>Enter the URL you want to shorten</small>
                </div>
                
                <div class="form-group">
                    <label for="custom_code">Custom Code (Optional)</label>
                    <div class="input-with-button">
                        <input type="text" id="custom_code" name="custom_code" 
                               placeholder="Leave empty for random generation"
                               value="' . htmlspecialchars($_POST['custom_code'] ?? '') . '">
                        <button type="button" id="generate-random" class="btn btn-small">Generate Random</button>
                    </div>
                    <small>Custom short code (e.g., "mylink"). Leave empty for auto-generation.</small>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Link Details</h3>
                
                <div class="form-group">
                    <label for="link_title">Title (Optional)</label>
                    <input type="text" id="link_title" name="link_title" 
                           placeholder="My Awesome Link"
                           value="' . htmlspecialchars($_POST['link_title'] ?? '') . '">
                </div>
                
                <div class="form-group">
                    <label for="link_excerpt">Description (Optional)</label>
                    <textarea id="link_excerpt" name="link_excerpt" rows="3" 
                              placeholder="Brief description of the link">' . htmlspecialchars($_POST['link_excerpt'] ?? '') . '</textarea>
                </div>
                
                <div class="form-group">
                    <label for="link_preview_url">Preview Image</label>
                    <div class="image-upload-container">
                        <input type="file" id="preview_image_file" name="preview_image_file" accept="image/*" class="image-upload-input">
                        <div class="image-upload-preview" id="preview-image-preview">
                            <span class="upload-text">Click to upload preview image or drag & drop</span>
                        </div>
                    </div>
                    <input type="hidden" id="link_preview_url" name="link_preview_url" value="' . htmlspecialchars($_POST['link_preview_url'] ?? '') . '">
                    <small>Upload an image that will be shown as preview</small>
                </div>
                
                <div class="form-group">
                    <label for="tag">Tag (Optional)</label>
                    <input type="text" id="tag" name="tag" 
                           placeholder="marketing, social, etc."
                           value="' . htmlspecialchars($_POST['tag'] ?? '') . '">
                </div>
            </div>
            
            <div class="form-section">
                <h3>Advertisement Settings</h3>
                
                <div class="form-group">
                    <label for="ads_img_url">Advertisement Image</label>
                    <div class="image-upload-container">
                        <input type="file" id="ads_image_file" name="ads_image_file" accept="image/*" class="image-upload-input">
                        <div class="image-upload-preview" id="image-preview">
                            <span class="upload-text">Click to upload image or drag & drop</span>
                        </div>
                    </div>
                    <input type="hidden" id="ads_img_url" name="ads_img_url" value="' . htmlspecialchars($_POST['ads_img_url'] ?? '') . '">
                    <small>Upload an image for advertisement display</small>
                </div>
                
                <div class="form-group">
                    <label for="ads_click_url">Advertisement Click URL (Optional)</label>
                    <input type="url" id="ads_click_url" name="ads_click_url" 
                           placeholder="https://example.com/ads"
                           value="' . htmlspecialchars($_POST['ads_click_url'] ?? '') . '">
                    <small>URL where users will be redirected when clicking the ad</small>
                </div>
                
                <div class="form-group">
                    <label for="ads_promoted_by">Promoted By (Optional)</label>
                    <input type="text" id="ads_promoted_by" name="ads_promoted_by" 
                           placeholder="Company Name or Brand"
                           value="' . htmlspecialchars($_POST['ads_promoted_by'] ?? '') . '">
                    <small>Name of the company or brand promoting the ad</small>
                </div>
            </div>
            
            <div class="form-section">
                <h3>Advanced Settings</h3>
                
                <div class="form-group">
                    <label for="redirect_type">Redirect Type</label>
                    <select id="redirect_type" name="redirect_type">
                        <option value="0" ' . (($_POST['redirect_type'] ?? '0') == '0' ? 'selected' : '') . '>Direct Redirect</option>
                        <option value="1" ' . (($_POST['redirect_type'] ?? '0') == '1' ? 'selected' : '') . '>Click Through</option>
                        <option value="2" ' . (($_POST['redirect_type'] ?? '0') == '2' ? 'selected' : '') . '>reCAPTCHA Protected</option>
                        <option value="3" ' . (($_POST['redirect_type'] ?? '0') == '3' ? 'selected' : '') . '>Password Protected</option>
                    </select>
                </div>
                
                <div class="form-group" id="password-group" style="display: none;">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           placeholder="Enter password for protected link">
                </div>
                
                <div class="form-group">
                    <label for="wait_seconds">Wait Time (seconds)</label>
                    <input type="number" id="wait_seconds" name="wait_seconds" 
                           min="0" max="60" value="' . ($_POST['wait_seconds'] ?? '10') . '">
                    <small>How long to wait before redirecting (0-60 seconds)</small>
                </div>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn">Create Link</button>
                <a href="/admin/links" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Show/hide password field based on redirect type
    const redirectType = document.getElementById("redirect_type");
    const passwordGroup = document.getElementById("password-group");
    
    function togglePasswordField() {
        if (redirectType.value === "3") {
            passwordGroup.style.display = "block";
            document.getElementById("password").required = true;
        } else {
            passwordGroup.style.display = "none";
            document.getElementById("password").required = false;
        }
    }
    
    redirectType.addEventListener("change", togglePasswordField);
    togglePasswordField(); // Initial call
    
    // Generate random code
    document.getElementById("generate-random").addEventListener("click", function() {
        const characters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        let result = "";
        for (let i = 0; i < 6; i++) {
            result += characters.charAt(Math.floor(Math.random() * characters.length));
        }
        document.getElementById("custom_code").value = result;
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
                imagePreview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
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
    
    // Click to upload
    imagePreview.addEventListener("click", function() {
        imageUploadInput.click();
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
                previewImagePreview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
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
    
    // Preview image click to upload
    previewImagePreview.addEventListener("click", function() {
        previewImageUploadInput.click();
    });
    
    // Open Graph extraction functionality
    document.getElementById("extract-og-btn").addEventListener("click", function() {
        const url = document.getElementById("next_url").value;
        if (!url) {
            alert("Please enter a URL first");
            return;
        }
        
        const button = this;
        const originalText = button.textContent;
        button.textContent = "Extracting...";
        button.disabled = true;
        
        fetch("/admin/extract-og", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "url=" + encodeURIComponent(url)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Fill in the form fields with extracted data
                if (data.data.title) {
                    document.getElementById("link_title").value = data.data.title;
                }
                if (data.data.description) {
                    document.getElementById("link_excerpt").value = data.data.description;
                }
                if (data.data.image) {
                    document.getElementById("link_preview_url").value = data.data.image;
                    // Show preview of the image
                    const preview = document.getElementById("preview-image-preview");
                    preview.innerHTML = `<img src="${data.data.image}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
                }
                alert("Open Graph data extracted successfully!");
            } else {
                alert("Failed to extract Open Graph data: " + (data.error || "Unknown error"));
            }
        })
        .catch(error => {
            console.error("Error:", error);
            alert("Failed to extract Open Graph data. Please try again.");
        })
        .finally(() => {
            button.textContent = originalText;
            button.disabled = false;
        });
    });
});
</script>
';

// Include the layout
include __DIR__ . '/layout.php';
?>