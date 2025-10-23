<div id="openModalBtn" onclick="modal.show()" class="btn">Mở trong điện thoại (QR Code)</div>
<div id="myModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p>Quét mã QR này bằng điện thoại của bạn</p>
    <div class="line"></div>
    <div id="qrcode"></div>
  </div>
</div>
<script type="text/javascript">
  new QRCode(document.getElementById("qrcode"), window.location.href);
</script>