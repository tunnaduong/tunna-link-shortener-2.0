<script type="text/javascript">
  var _captchaTries = 0;
  var onloadCallback = function () {
    _captchaTries++;
    if (_captchaTries > 9)
      return;
    if ($('.g-recaptcha').length > 0) {
      grecaptcha.render("recaptcha", {
        sitekey: '<?= htmlspecialchars($recaptcha_site_key) ?>',
        callback: function () {
          console.log('recaptcha callback');
        }
      });
      return;
    }
    window.setTimeout(recaptchaOnload, 1000);
  };
</script>