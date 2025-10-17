<script>
  var width = window.screen.width;
  var height = window.screen.height;
  var referrer = document.referrer;
  var data = {
    id: "<?= htmlspecialchars($link->getCode()) ?>",
    size: width + 'x' + height,
    ref: referrer
  };
  $.ajax({
    type: "POST",
    url: "/api/tracker",
    data: data,
    success: function (response) {
      console.log(response);
    }
  });
  if (/^\?fbclid=/.test(location.search)) {
    location.replace(location.href.replace(/\?fbclid.+/, ""));
  }
</script>