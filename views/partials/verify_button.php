<button type="submit" id="next_btn" class="btn btn-primary disabled-button" style="min-width: 304px;">
  Vui lòng đợi <?= $link->getWaitSeconds() ?> giây...
</button>
<script type="text/javascript">
  var remainingTime = <?= $link->getWaitSeconds() ?>;
  var timer = setInterval(() => {
    remainingTime--;
    const button = document.getElementById('next_btn');
    button.innerText = "Vui lòng đợi " + remainingTime + " giây...";
    if (remainingTime <= 0) {
      clearInterval(timer);
      button.classList.remove('disabled-button');
      button.innerText = "Xác minh và tiếp tục!";
    }
  }, <?= $link->getCountdownDelay() ?>);
</script>