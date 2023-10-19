<?php
// To call this page, in the browser type:
// http://localhost/

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/head.php';
    ?>
</head>

<body onclick="">
    <center onclick>
        <h1>Link Shortener</h1>
        <div id="next_btn" onclick="alert('Chức năng đang hoàn thiện!')" class="btn btn-primary disabled-button">Vui lòng đợi 6 giây...</div>
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
        <a href="#link_info">
            <div onclick class="btn">Xem thông tin chi tiết link</div>
        </a>
        <h2><span><i class="fas fa-share"></i> Chia sẻ</span></h2>
        <div class="social">
            <i onclick="alert('Chức năng đang hoàn thiện!')" class="fab fa-facebook"></i>
            <i onclick="alert('Chức năng đang hoàn thiện!')" class="fab fa-twitter-square"></i>
            <i onclick="alert('Chức năng đang hoàn thiện!')" class="fas fa-copy"></i>
        </div>
        <div class="ads">
            <div id="awn-z7610822"></div>
        </div>
        <h3 id="link_info">Thông tin liên kết</h3>
        <table class="table">
            <tbody>
                <tr>
                    <td>ID</td>
                    <td>gido</td>
                </tr>
                <tr>
                    <td>Mật khẩu</td>
                    <td>Không có</td>
                </tr>
                <tr>
                    <td>Lượt truy cập</td>
                    <td>43532</td>
                </tr>
                <tr>
                    <td>Thời gian tạo</td>
                    <td>9 tháng trước</td>
                </tr>
            </tbody>
        </table>
        <p class="tag">Thẻ:
            <span class="badge">gì đó</span>
            <span class="badge">test</span>
            <span class="badge">thử nghiệm</span>
        </p>
        <div>
            <script data-cfasync="false" type="text/javascript" src="//brightonclick.com/a/display.php?r=7610902"></script>
        </div>
        <footer>
            <p class="footer--copyright">
                <span id="footer--mobile">© 2023 Duong Tung Anh<br /><span style="color: white; font-weight: 300; font-size: 15px">All rights reserved</span></span>
                <span id="footer--desktop">© 2023 Duong Tung Anh. All rights reserved.</span>
            </p>
            <p class="footer--fun">
                Được làm bằng 💕 <i>tình yêu</i>, 🔥 <i>nhiệt huyết</i>, ⌨️
                <i>bàn phím</i> và rất nhiều ☕️ <i>cà phê</i>.
            </p>
        </footer>
    </center>
    <?php
    require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
    ?>
</body>

</html>