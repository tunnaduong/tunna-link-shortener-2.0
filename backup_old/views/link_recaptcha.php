<center>
  <h1>Link Shortener</h1>
  <h4>Xác minh bạn không phải là robot để tiếp tục...</h4>
  <?php if (isset($error)): ?>
    <div class='alert-danger'><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method='post' action="">
    <div id="recaptcha" class="g-recaptcha" data-sitekey="<?= htmlspecialchars($recaptcha_site_key) ?>"
      style="margin-bottom: 8px"></div>
    <?= $this->renderPartial('verify_button', ['link' => $link]) ?>
  </form>
  <?= $this->renderPartial('qr_code_section') ?>
  <a href="#link_info" class="scroll-link">
    <div class="btn">Xem thông tin chi tiết link</div>
  </a>
  <?= $this->renderPartial('share_options') ?>
  <?= $this->renderPartial('ads', ['link' => $link]) ?>
  <?= $this->renderPartial('link_info', ['link' => $link, 'visit_count' => $visit_count]) ?>
  <?= $this->renderPartial('ads_scripts') ?>
  <?= $this->renderPartial('tags', ['link' => $link]) ?>
  <?= $this->renderPartial('footer') ?>
  <?= $this->renderPartial('tracking_script', ['link' => $link]) ?>
  <?= $this->renderPartial('recaptcha_script', ['recaptcha_site_key' => $recaptcha_site_key]) ?>
</center>