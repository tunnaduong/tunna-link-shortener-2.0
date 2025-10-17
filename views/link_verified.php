<center>
  <h1>Link Shortener</h1>
  <?= $this->renderPartial('continue_button', ['link' => $link]) ?>
  <?= $this->renderPartial('qr_code_section') ?>
  <a href="#link_info" class="scroll-link">
    <div class="btn">Xem thông tin chi tiết link</div>
  </a>
  <?= $this->renderPartial('share_options') ?>
  <?= $this->renderPartial('ads', ['link' => $link]) ?>
  <?= $this->renderPartial('visit_button_immediate', ['link' => $link]) ?>
  <?= $this->renderPartial('link_info', ['link' => $link, 'visit_count' => $visit_count]) ?>
  <?= $this->renderPartial('ads_scripts') ?>
  <?= $this->renderPartial('tags', ['link' => $link]) ?>
  <?= $this->renderPartial('footer') ?>
  <?= $this->renderPartial('tracking_script', ['link' => $link]) ?>
</center>