<a href="#ads" id="next_btn" class="scroll-link btn btn-primary disabled-button">
  Vui lòng đợi {{ $wait_seconds }} giây...
</a>
<script type="text/javascript">
  var remainingTime = {{ $wait_seconds }};
  var timer = setInterval(() => {
    remainingTime--;
    const button = document.getElementById('next_btn');
    button.innerText = "Vui lòng đợi " + remainingTime + " giây...";
    if (remainingTime <= 0) {
      clearInterval(timer);
      button.classList.remove('disabled-button');
      button.innerText = "Bấm vào đây để tiếp tục!";
    }
  }, {{ $countdown_delay }});
</script>