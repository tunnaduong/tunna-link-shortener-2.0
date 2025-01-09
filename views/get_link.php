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
        // Get the current website URL
        $currentUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        ?>
        <title><?php echo isset($row['link_title']) ? $row['link_title'] : "Tunna Duong Link Shortener" ?></title>
        <meta property="og:title" content="<?php echo $row['link_title'] ?>" />
        <meta property="og:description" content="<?php echo isset($row['link_excerpt']) ? $row['link_excerpt'] : "Công cụ rút gọn link được tạo bởi Tunna Duong" ?>" />
        <meta property="og:type" content="website.url-shortener" />
        <meta property="og:url" content="<?php echo $currentUrl ?>" />
        <meta property="og:image" content="<?php echo isset($row['link_preview_url']) ? $row['link_preview_url'] : "/assets/images/link.jpg" ?>" />
    </head>

    <body onclick="">
        <center onclick>
            <h1>Link Shortener</h1>
            <div id="next_btn" onclick="openNewWindow(`<?php echo $row['next_url'] ?>`)" class="btn btn-primary disabled-button">Vui lòng đợi 6 giây...</div>
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
                <i onclick="twitterShare()" class="fab fa-twitter-square"></i>
                <i onclick="copyPageUrl()" class="fas fa-copy"></i>
            </div>
            <a href="<?= $row['ads_click_url'] ?? "https://www.youtube.com/c/HAMH%E1%BB%8CC" ?>">
                <img class="ads" src="<?= isset($row['ads_img_url']) ? $row['ads_img_url'] : "/assets/images/hamhoc_ads_campaign.jpg" ?>" alt="Ads" width="550">
            </a>
            <div>
                <sup style="color: lightgray;">Quảng cáo tài trợ bởi: <?= $row['ads_promoted_by'] ?? "Ham Học Channel" ?></sup>
            </div>
            <h3 id="link_info">Thông tin liên kết</h3>
            <table class="table" style="margin-bottom: 20px;">
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
            <script type="text/javascript">
                atOptions = {
                    'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                    'format': 'iframe',
                    'height': 250,
                    'width': 300,
                    'params': {}
                };
            </script>
            <script type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
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
            <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
            <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
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
                if (s == 0 || s < 0) {
                    clearInterval(timer);
                    $("#next_btn").removeClass("disabled-button");
                    button.text("Bấm vào đây để tiếp tục!")
                }
            }, <?php echo $row['countdown_delay']; ?>);

            var width = window.screen.width;
            var height = window.screen.height;
            // Get the referrer URL
            const referrer = document.referrer;
            // Define the data to be sent to the PHP script
            const data = {
                id: "<?php echo $row['code'] ?>",
                size: width + 'x' + height,
                ref: referrer
            };

            // Send an AJAX request to the PHP script with the data as a parameter
            $.ajax({
                type: "POST",
                url: "/api/tracker",
                data: data,
                success: function(response) {
                    // Handle the response from the PHP script
                    console.log(response);
                }
            });

            if (/^\?fbclid=/.test(location.search)) {
                location.replace(location.href.replace(/\?fbclid.+/, ""));
            }
        </script>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
        ?>
    </body>

    </html>
    <?php
    if ($row['redirect_type'] == 0) {
        $link = $row['next_url'];
        // header('Location: ' . $link);
        echo "<script>
        setTimeout(() => {
            window.location.href = '$link';
        }, 100);
        </script>";
    }
} else {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/head.php';
        // Get the current website URL
        $currentUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        ?>
        <title><?php echo isset($row['link_title']) ? $row['link_title'] : "Tunna Duong Link Shortener" ?></title>
        <meta property="og:title" content="<?php echo $row['link_title'] ?>" />
        <meta property="og:description" content="<?php echo isset($row['link_excerpt']) ? $row['link_excerpt'] : "Công cụ rút gọn link được tạo bởi Tunna Duong" ?>" />
        <meta property="og:type" content="website.url-shortener" />
        <meta property="og:url" content="<?php echo $currentUrl ?>" />
        <meta property="og:image" content="<?php echo isset($row['link_preview_url']) ? $row['link_preview_url'] : "/assets/images/link.jpg" ?>" />
    </head>

    <body onclick="">
        <center onclick>
            <h1>Link Shortener</h1>
            <img src="/assets/images/404.jpg" class="_404 dude" alt="404 Not Found">
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
<?php
}
$conn->close();

?>