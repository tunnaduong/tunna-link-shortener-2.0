<div data-url="<?= htmlspecialchars($link->getNextUrl(), ENT_QUOTES) ?>" style="margin-top: 10px" id="next_btn2"
  class="btn btn-primary">
  Mở liên kết!
</div>
<script>
  document.getElementById('next_btn2').addEventListener('click', function () {
    openNewWindow(this.getAttribute('data-url'));
  });
</script>