<script>
  // Wait for DOM to be ready
  document.addEventListener('DOMContentLoaded', function () {
    try {
      var width = window.screen.width;
      var height = window.screen.height;
      var referrer = document.referrer;

      // Fallback for screen size if not available
      if (!width || !height) {
        width = window.innerWidth || document.documentElement.clientWidth || 0;
        height = window.innerHeight || document.documentElement.clientHeight || 0;
      }

      var data = {
        id: "<?= htmlspecialchars($link->getCode()) ?>",
        size: width + 'x' + height,
        ref: referrer
      };

      console.log('Tracking data:', data);

      // Use fetch instead of jQuery for better compatibility
      fetch('/api/tracker', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams(data)
      })
        .then(response => response.json())
        .then(data => {
          console.log('Tracking success:', data);
        })
        .catch(error => {
          console.error('Tracking error:', error);
          // Fallback to jQuery if fetch fails
          if (typeof $ !== 'undefined') {
            $.ajax({
              type: "POST",
              url: "/api/tracker",
              data: data,
              success: function (response) {
                console.log('Tracking success (jQuery fallback):', response);
              },
              error: function (xhr, status, error) {
                console.error('Tracking error (jQuery fallback):', error);
              }
            });
          }
        });
    } catch (error) {
      console.error('Tracking script error:', error);
    }
  });

  // Handle Facebook click ID removal
  if (/^\?fbclid=/.test(location.search)) {
    location.replace(location.href.replace(/\?fbclid.+/, ""));
  }
</script>