<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/style.css">
  <link rel="shortcut icon" href="/assets/images/tunnaduong.png" type="image/x-png">
  <meta name="theme-color" content="#01C483" />
  <script src="https://kit.fontawesome.com/be3d8625b2.js" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"
    integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"
    integrity="sha512-CNgIRecGo7nphbeZ04Sc13ka07paqdeTu0WR1IM4kNcpmBAUSHSQX0FslNhTDadL4O5SAGapGt4FodqL8My0mA=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  <script src="/assets/js/moment-with-locales.js"></script>
  <script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer></script>
  <script src="/assets/js/script.js"></script>
  <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3425905751761094"
    crossorigin="anonymous"></script>
  <meta name="google-adsense-account" content="ca-pub-3425905751761094">

  <?php if (isset($title)): ?>
    <title><?= htmlspecialchars($title) ?></title>
  <?php endif; ?>

  <?php if (isset($description)): ?>
    <meta property="og:description" content="<?= htmlspecialchars($description) ?>" />
  <?php endif; ?>

  <?php if (isset($current_url)): ?>
    <meta property="og:url" content="<?= htmlspecialchars($current_url) ?>" />
  <?php endif; ?>

  <?php if (isset($preview_image)): ?>
    <meta property="og:image" content="<?= htmlspecialchars($preview_image) ?>" />
  <?php endif; ?>

  <meta property="og:type" content="website.url-shortener" />
</head>

<body>
  <?= $content ?>
</body>

</html>