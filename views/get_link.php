<?php
// To call this page, in the browser type:
// http://localhost/$id
require_once $_SERVER['DOCUMENT_ROOT'] . '/serverconnect.php';

$sql = "SELECT * FROM links WHERE code = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = mysqli_fetch_assoc($result);
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
            <div id="next_btn" onclick="location.href='<?php echo $row['next_url'] ?>'" class="btn btn-primary disabled-button">Vui lòng đợi 6 giây...</div>
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
            <a href="#link_info" class="scroll-link">
                <div onclick class="btn">Xem thông tin chi tiết link</div>
            </a>
            <h2><span><i class="fas fa-share"></i> Chia sẻ</span></h2>
            <div class="social">
                <i onclick="fbShare()" class="fab fa-facebook"></i>
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
                        <td><?php echo $row['code'] ?></td>
                    </tr>
                    <tr>
                        <td>Tiêu đề</td>
                        <td><?php echo $row['link_title'] ?></td>
                    </tr>
                    <tr>
                        <td>Mô tả</td>
                        <td><?php echo $row['link_excerpt'] ?></td>
                    </tr>
                    <tr>
                        <td>Mật khẩu</td>
                        <td><?php echo isset($row['password']) ? "Có" : "Không có"; ?></td>
                    </tr>
                    <tr>
                        <td>Lượt truy cập</td>
                        <td><?php
                            $sql = "SELECT count(*) as total FROM tracker WHERE ref_code = '$id'";
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $row2 = mysqli_fetch_assoc($result);
                                echo $row2['total'];
                            } else {
                                echo "0";
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>Thời gian tạo</td>
                        <td id="time"></td>
                        <script>
                            var time = moment("<?php echo $row['created_at'] ?>").locale('vi').fromNow();
                            document.getElementById("time").innerHTML = time;
                        </script>
                    </tr>
                </tbody>
            </table>
            <p class="tag">Thẻ:
                <?php
                $tags = explode(",", $row['tag']);
                foreach ($tags as $tag) {
                    echo "<span class='badge'>$tag</span>";
                }
                ?>
                <!-- <span class="badge">gì đó</span>
                <span class="badge">test</span>
                <span class="badge">thử nghiệm</span> -->
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
        <script>
            // Get the button element
            const button = $("#next_btn");

            var s = <?php echo $row['wait_seconds']; ?>;

            var timer = setInterval(() => {
                s--;
                button.text("Vui lòng đợi " + s + " giây...");
                if (s == 0) {
                    clearInterval(timer);
                    $("#next_btn").removeClass("disabled-button");
                }
            }, <?php echo $row['countdown_delay']; ?>);

            // Set a 6-second timer
            setTimeout(() => {
                // Change the button text after 6 seconds
                button.text("Bấm vào đây để tiếp tục!");
            }, (<?php echo $row['redirect_delay']; ?> - 2000));
        </script>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
        ?>
    </body>

    </html>
<?php
} else {
    echo "404 Not Found";
}
$conn->close();

?>