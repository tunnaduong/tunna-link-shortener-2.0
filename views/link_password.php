<center>
  <h1>Link Shortener</h1>
  <h4>Cần có mật khẩu để xem liên kết này</h4>
  <?php if (isset($error)): ?>
    <div class='alert-danger'><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <form method='post' action="">
    <div class='form-group'>
      <input class="pw" type='password' placeholder="Nhập mật khẩu..." name='password'
        style="max-width: 304px;width: 100%;box-sizing: border-box;">
    </div>
    <button type='submit' class='btn btn-primary' style="min-width: 304px;">Xác nhận</button>
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
</center>