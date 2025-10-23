<div id="next_btn2" data-url="{{ $link->getNextUrl() }}" style="margin-top: 10px"
  class="btn disabled-button btn-primary">
  Vui lòng đợi {{ $link->getWaitSeconds() }} giây...
</div>
<script type="text/javascript">
  var remainingTime2 = {{ $link-> getWaitSeconds() }};
  var timer2 = setInterval(() => {
    remainingTime2--;
    const button2 = document.getElementById('next_btn2');
    button2.innerText = "Vui lòng đợi " + remainingTime2 + " giây...";
    if (remainingTime2 <= 0) {
      clearInterval(timer2);
      button2.classList.remove('disabled-button');
      button2.innerText = "Mở liên kết!";
      // Add click event listener when button becomes active
      button2.addEventListener('click', function () {
        openNewWindow(this.getAttribute('data-url'));
      });
    }
  }, {{ $link-> getCountdownDelay() }});
</script>