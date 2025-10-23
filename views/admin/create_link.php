<?php
// Set page title
$pageTitle = 'Create New Link';
?>

<div class="create-link-page">
  <div class="page-header">
    <h2>Create New Link</h2>
    <a href="/admin/links" class="btn btn-secondary">Back to Links</a>
  </div>

  <!-- Tab Navigation -->
  <div class="tab-navigation">
    <button class="tab-btn active" onclick="switchTab('manual', this)">Manual</button>
    <button class="tab-btn" onclick="switchTab('batch', this)">Batch</button>
  </div>

  <!-- Manual Tab -->
  <div id="manual-tab" class="tab-content active">
    <div class="create-link-form-container">
      <form method="POST" action="/admin/create-link" class="create-link-form" enctype="multipart/form-data">
        <div class="form-section">
          <h3>Basic Information</h3>

          <div class="form-group">
            <label for="next_url">Destination URL *</label>
            <div class="url-input-group">
              <input type="url" id="next_url" name="next_url" required placeholder="https://example.com"
                value="<?php echo htmlspecialchars($_POST['next_url'] ?? ''); ?>">
              <button type="button" id="extract-og-btn" class="btn btn-small">Extract Open Graph</button>
            </div>
            <small>Enter the URL you want to shorten</small>
          </div>

          <div class="form-group">
            <label for="custom_code">Custom Code (Optional)</label>
            <div class="input-with-button">
              <input type="text" id="custom_code" name="custom_code" placeholder="Leave empty for random generation"
                value="<?php echo htmlspecialchars($_POST['custom_code'] ?? ''); ?>">
              <button type="button" id="generate-random" class="btn btn-small">Generate Random</button>
            </div>
            <small>Custom short code (e.g., "mylink"). Leave empty for auto-generation.</small>
          </div>
        </div>

        <div class="form-section">
          <h3>Link Details</h3>

          <div class="form-group">
            <label for="link_title">Title (Optional)</label>
            <input type="text" id="link_title" name="link_title" placeholder="My Awesome Link"
              value="<?php echo htmlspecialchars($_POST['link_title'] ?? ''); ?>">
          </div>

          <div class="form-group">
            <label for="link_excerpt">Description (Optional)</label>
            <textarea id="link_excerpt" name="link_excerpt" rows="3"
              placeholder="Brief description of the link"><?php echo htmlspecialchars($_POST['link_excerpt'] ?? ''); ?></textarea>
          </div>

          <div class="form-group">
            <label for="link_preview_url">Preview Image</label>
            <div class="image-upload-container">
              <input type="file" id="preview_image_file" name="preview_image_file" accept="image/*"
                class="image-upload-input">
              <div class="image-upload-preview" id="preview-image-preview">
                <span class="upload-text">Click to upload preview image or drag & drop</span>
              </div>
            </div>
            <input type="hidden" id="link_preview_url" name="link_preview_url"
              value="<?php echo htmlspecialchars($_POST['link_preview_url'] ?? ''); ?>">
            <small>Upload an image that will be shown as preview</small>
          </div>

          <div class="form-group">
            <label for="tag">Tag (Optional)</label>
            <input type="text" id="tag" name="tag" placeholder="marketing, social, etc."
              value="<?php echo htmlspecialchars($_POST['tag'] ?? ''); ?>">
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
            <input type="hidden" id="ads_img_url" name="ads_img_url"
              value="<?php echo htmlspecialchars($_POST['ads_img_url'] ?? ''); ?>">
            <small>Upload an image for advertisement display</small>
          </div>

          <div class="form-group">
            <label for="ads_click_url">Advertisement Click URL (Optional)</label>
            <input type="url" id="ads_click_url" name="ads_click_url" placeholder="https://example.com/ads"
              value="<?php echo htmlspecialchars($_POST['ads_click_url'] ?? ''); ?>">
            <small>URL where users will be redirected when clicking the ad</small>
          </div>

          <div class="form-group">
            <label for="ads_promoted_by">Promoted By (Optional)</label>
            <input type="text" id="ads_promoted_by" name="ads_promoted_by" placeholder="Company Name or Brand"
              value="<?php echo htmlspecialchars($_POST['ads_promoted_by'] ?? ''); ?>">
            <small>Name of the company or brand promoting the ad</small>
          </div>
        </div>

        <div class="form-section">
          <h3>Advanced Settings</h3>

          <div class="form-group">
            <label for="redirect_type">Redirect Type</label>
            <select id="redirect_type" name="redirect_type">
              <option value="0" <?php echo (($_POST['redirect_type'] ?? '0') == '0') ? 'selected' : ''; ?>>Direct Redirect
              </option>
              <option value="1" <?php echo (($_POST['redirect_type'] ?? '0') == '1') ? 'selected' : ''; ?>>Click Through
              </option>
              <option value="2" <?php echo (($_POST['redirect_type'] ?? '0') == '2') ? 'selected' : ''; ?>>reCAPTCHA
                Protected</option>
              <option value="3" <?php echo (($_POST['redirect_type'] ?? '0') == '3') ? 'selected' : ''; ?>>Password
                Protected</option>
            </select>
          </div>

          <div class="form-group" id="password-group" style="display: none;">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter password for protected link">
          </div>

          <div class="form-group">
            <label for="wait_seconds">Wait Time (seconds)</label>
            <input type="number" id="wait_seconds" name="wait_seconds" min="0" max="60"
              value="<?php echo $_POST['wait_seconds'] ?? '10'; ?>">
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

  <!-- Batch Tab -->
  <div id="batch-tab" class="tab-content">
    <div class="batch-shorten-container">
      <h3>Batch URL Shortening</h3>
      <p>Enter URLs to shorten, one per line. You can use simple URLs or the advanced format with pipe separators.</p>

      <div class="batch-format-info">
        <h4>Supported Formats:</h4>
        <ul>
          <li><strong>Simple:</strong> <code>https://example.com</code></li>
          <li><strong>Advanced:</strong> <code>https://example.com|type|wait|password|tag</code></li>
          <li><strong>With Custom Code:</strong> <code>https://example.com|myshortcode|type|wait|password|tag</code>
          </li>
        </ul>
        <p><strong>Parameters:</strong> custom_code (optional), type (0=direct, 1=click, 2=captcha, 3=password), wait
          (seconds), password, tag
        </p>
      </div>

      <form id="batch-form" method="POST" action="/admin/batch-shorten">
        <div class="form-group">
          <label for="batch_urls">URLs to Shorten</label>
          <textarea id="batch_urls" name="urls" rows="10"
            placeholder="https://example.com&#10;https://another-site.com|1|5|mypassword|marketing&#10;https://third-site.com|myshortcode|1|5|password|marketing&#10;https://fourth-site.com"></textarea>
          <small>Enter one URL per line. Use the advanced format for custom settings.</small>
        </div>

        <div class="form-group">
          <label for="default_redirect_type">Default Redirect Type</label>
          <select id="default_redirect_type" name="default_redirect_type">
            <option value="0">Direct Redirect</option>
            <option value="1">Click Through</option>
            <option value="2">reCAPTCHA Protected</option>
            <option value="3">Password Protected</option>
          </select>
        </div>

        <div class="form-group">
          <label for="default_wait_seconds">Default Wait Time (seconds)</label>
          <input type="number" id="default_wait_seconds" name="default_wait_seconds" min="0" max="60" value="10">
        </div>

        <div class="form-section">
          <h3>Advertisement Settings (Applied to All URLs)</h3>

          <div class="form-group">
            <label for="batch_ads_img_url">Advertisement Image</label>
            <div class="image-upload-container">
              <input type="file" id="batch_ads_image_file" name="batch_ads_image_file" accept="image/*"
                class="image-upload-input">
              <div class="image-upload-preview" id="batch-image-preview">
                <span class="upload-text">Click to upload image or drag & drop</span>
              </div>
            </div>
            <input type="hidden" id="batch_ads_img_url" name="batch_ads_img_url" value="">
            <small>Upload an image for advertisement display (applied to all URLs)</small>
          </div>

          <div class="form-group">
            <label for="batch_ads_click_url">Ad Click URL</label>
            <input type="url" id="batch_ads_click_url" name="batch_ads_click_url" placeholder="https://example.com/ads">
            <small>URL where users will be redirected when clicking the ad</small>
          </div>

          <div class="form-group">
            <label for="batch_ads_promoted_by">Promoted By</label>
            <input type="text" id="batch_ads_promoted_by" name="batch_ads_promoted_by"
              placeholder="Company Name or Brand">
            <small>Name of the company or brand promoting the ad</small>
          </div>
        </div>

        <div class="form-actions">
          <button type="submit" class="btn">Shorten All URLs</button>
          <button type="button" class="btn btn-secondary" onclick="clearBatchForm()">Clear</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  // Tab switching functionality
  function switchTab(tabName, element) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
      tab.classList.remove('active');
    });

    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-btn').forEach(btn => {
      btn.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName + '-tab').classList.add('active');

    // Add active class to clicked button
    element.classList.add('active');
  }

  // Clear batch form
  function clearBatchForm() {
    document.getElementById('batch_urls').value = '';
    document.getElementById('batch_ads_click_url').value = '';
    document.getElementById('batch_ads_promoted_by').value = '';
    document.getElementById('batch_ads_img_url').value = '';
    document.getElementById('batch_ads_image_file').value = '';
    document.getElementById('batch-image-preview').innerHTML = '<span class="upload-text">Click to upload image or drag & drop</span>';
  }

  document.addEventListener("DOMContentLoaded", function () {
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
    document.getElementById("generate-random").addEventListener("click", function () {
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

    imageUploadInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          imagePreview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
      }
    });

    // Drag and drop functionality
    imagePreview.addEventListener("dragover", function (e) {
      e.preventDefault();
      imagePreview.style.backgroundColor = "#f0f0f0";
    });

    imagePreview.addEventListener("dragleave", function (e) {
      e.preventDefault();
      imagePreview.style.backgroundColor = "";
    });

    imagePreview.addEventListener("drop", function (e) {
      e.preventDefault();
      imagePreview.style.backgroundColor = "";
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        imageUploadInput.files = files;
        imageUploadInput.dispatchEvent(new Event("change"));
      }
    });

    // Click to upload
    imagePreview.addEventListener("click", function () {
      imageUploadInput.click();
    });

    // Preview image upload functionality
    const previewImageUploadInput = document.getElementById("preview_image_file");
    const previewImagePreview = document.getElementById("preview-image-preview");
    const linkPreviewUrlInput = document.getElementById("link_preview_url");

    previewImageUploadInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          previewImagePreview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
        };
        reader.readAsDataURL(file);
      }
    });

    // Preview image drag and drop functionality
    previewImagePreview.addEventListener("dragover", function (e) {
      e.preventDefault();
      previewImagePreview.style.backgroundColor = "#f0f0f0";
    });

    previewImagePreview.addEventListener("dragleave", function (e) {
      e.preventDefault();
      previewImagePreview.style.backgroundColor = "";
    });

    previewImagePreview.addEventListener("drop", function (e) {
      e.preventDefault();
      previewImagePreview.style.backgroundColor = "";
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        previewImageUploadInput.files = files;
        previewImageUploadInput.dispatchEvent(new Event("change"));
      }
    });

    // Preview image click to upload
    previewImagePreview.addEventListener("click", function () {
      previewImageUploadInput.click();
    });

    // Batch ads image upload functionality
    const batchAdsImageUploadInput = document.getElementById("batch_ads_image_file");
    const batchAdsImagePreview = document.getElementById("batch-image-preview");
    const batchAdsImgUrlInput = document.getElementById("batch_ads_img_url");

    batchAdsImageUploadInput.addEventListener("change", function (e) {
      const file = e.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function (e) {
          batchAdsImagePreview.innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 200px; border-radius: 4px;">`;
          batchAdsImgUrlInput.value = e.target.result;
        };
        reader.readAsDataURL(file);
      }
    });

    // Batch ads image drag and drop functionality
    batchAdsImagePreview.addEventListener("dragover", function (e) {
      e.preventDefault();
      batchAdsImagePreview.style.backgroundColor = "#f0f0f0";
    });

    batchAdsImagePreview.addEventListener("dragleave", function (e) {
      e.preventDefault();
      batchAdsImagePreview.style.backgroundColor = "";
    });

    batchAdsImagePreview.addEventListener("drop", function (e) {
      e.preventDefault();
      batchAdsImagePreview.style.backgroundColor = "";
      const files = e.dataTransfer.files;
      if (files.length > 0) {
        batchAdsImageUploadInput.files = files;
        batchAdsImageUploadInput.dispatchEvent(new Event("change"));
      }
    });

    // Click to upload batch ads image
    batchAdsImagePreview.addEventListener("click", function () {
      batchAdsImageUploadInput.click();
    });

    // Auto-extract OpenGraph on paste
    document.getElementById("next_url").addEventListener("paste", function (e) {
      setTimeout(() => {
        if (this.value && this.value.trim()) {
          // Show loading indicator
          const button = document.getElementById("extract-og-btn");
          const originalText = button.textContent;
          button.textContent = "Auto-extracting...";
          button.disabled = true;

          // Trigger extraction
          document.getElementById("extract-og-btn").click();
        }
      }, 100);
    });

    // Open Graph extraction functionality
    document.getElementById("extract-og-btn").addEventListener("click", function () {
      const url = document.getElementById("next_url").value;
      if (!url) {
        alert("Please enter a URL first");
        return;
      }

      const button = this;
      const originalText = button.textContent;
      button.textContent = "Extracting...";
      button.disabled = true;

      // Create AbortController for timeout
      const controller = new AbortController();
      const timeoutId = setTimeout(() => controller.abort(), 15000); // 15 second timeout

      fetch("/admin/extract-og", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "url=" + encodeURIComponent(url),
        signal: controller.signal
      })
        .then(response => response.json())
        .then(data => {
          clearTimeout(timeoutId); // Clear the timeout
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

            // Display all extracted Open Graph tags
            showExtractedTags(data.data);

            if (data.warning) {
              alert("Open Graph data extracted with limitations: " + data.warning);
            } else {
              alert("Open Graph data extracted successfully!");
            }
          } else {
            alert("Failed to extract Open Graph data: " + (data.error || "Unknown error"));
          }
        })
        .catch(error => {
          clearTimeout(timeoutId); // Clear the timeout
          console.error("Error:", error);

          if (error.name === "AbortError") {
            alert("Request timed out. The website may be slow or blocking requests. Please try again or enter the information manually.");
          } else {
            alert("Failed to extract Open Graph data. Please try again.");
          }
        })
        .finally(() => {
          button.textContent = originalText;
          button.disabled = false;
        });
    });

    // Function to display all extracted Open Graph tags
    function showExtractedTags(data) {
      // Create or update the extracted tags display
      let tagsContainer = document.getElementById("extracted-tags-container");
      if (!tagsContainer) {
        tagsContainer = document.createElement("div");
        tagsContainer.id = "extracted-tags-container";
        tagsContainer.style.cssText = "margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border: 1px solid #dee2e6;";

        const form = document.querySelector("form");
        form.insertBefore(tagsContainer, form.querySelector(".form-actions"));
      }

      // Clear previous content
      tagsContainer.innerHTML = "";

      // Add title
      const title = document.createElement("h4");
      title.textContent = "📋 Extracted Open Graph Tags";
      title.style.cssText = "margin: 0 0 15px 0; color: #495057;";
      tagsContainer.appendChild(title);

      // Create table to display all tags
      const table = document.createElement("table");
      table.style.cssText = "width: 100%; border-collapse: collapse; font-size: 14px;";

      // Add header
      const headerRow = document.createElement("tr");
      headerRow.style.cssText = "background: #e9ecef;";
      headerRow.innerHTML = "<th style=\"padding: 8px; text-align: left; border: 1px solid #dee2e6;\">Tag</th><th style=\"padding: 8px; text-align: left; border: 1px solid #dee2e6;\">Value</th>";
      table.appendChild(headerRow);

      // Add all extracted tags
      Object.keys(data).forEach(key => {
        if (data[key] && key !== "url") {
          const row = document.createElement("tr");
          row.style.cssText = "border-bottom: 1px solid #dee2e6;";

          const tagCell = document.createElement("td");
          tagCell.style.cssText = "padding: 8px; font-weight: 500; color: #6c757d; border: 1px solid #dee2e6;";
          tagCell.textContent = key;

          const valueCell = document.createElement("td");
          valueCell.style.cssText = "padding: 8px; border: 1px solid #dee2e6; word-break: break-all;";

          // Handle image tags specially
          if (key === "image" || key === "og:image") {
            valueCell.innerHTML = `<img src="${data[key]}" style="max-width: 200px; max-height: 100px; border-radius: 4px; display: block; margin-top: 5px;">`;
          } else {
            valueCell.textContent = data[key];
          }

          row.appendChild(tagCell);
          row.appendChild(valueCell);
          table.appendChild(row);
        }
      });

      tagsContainer.appendChild(table);
    }
  });
</script>

<?php
// The layout is handled by ViewRenderer
// No need to include layout.php directly
?>