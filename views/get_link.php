<?php
// To call this page, in the browser type:
// http://localhost/$id
require_once $_SERVER['DOCUMENT_ROOT'] . '/serverconnect.php';

$sql = "SELECT * FROM links WHERE code = '$id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = mysqli_fetch_assoc($result);
    if ($row['redirect_type'] == 0) {
        $link = $row['next_url'];
        header('Location: ' . $link);
    }

    function getOS()
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $osPlatform    =   "Unknown";

        $osArray       =   array(
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );

        foreach ($osArray as $regex => $value) {

            if (preg_match($regex, $userAgent)) {
                $osPlatform    =   $value;
            }
        }

        return $osPlatform;
    }

    $operating_system = getOS();

    // if there is a http referer header, then set the "ref_url" variable to the value of the header else set it to "Unknown"
    if (isset($_GET['ref'])) {
        $ref_url = $_GET['ref'];
    } else {
        $ref_url = "Unknown";
    }

    // if the page is refreshed, set the ref_url to "Page refreshed"
    if (isset($_SERVER['HTTP_CACHE_CONTROL']) && $_SERVER['HTTP_CACHE_CONTROL'] == 'max-age=0') {
        $ref_url = "Page refreshed";
    }

    // check for user's browser name
    if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== FALSE) {
        $browser = 'Internet Explorer';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Trident') !== FALSE) { //For Supporting IE 11
        $browser = 'Internet Explorer';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Firefox') !== FALSE) {
        $browser = 'Mozilla Firefox';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') !== FALSE) {
        $browser = 'Google Chrome';
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera Mini') !== FALSE) {
        $browser = "Opera Mini";
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Opera') !== FALSE) {
        $browser = "Opera";
    } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'Safari') !== FALSE) {
        $browser = "Safari";
    } else {
        $browser = 'Other';
    }

    // get user's IP address
    function getUserIP()
    {
        // Get real visitor IP behind CloudFlare network
        if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) {
            $_SERVER['REMOTE_ADDR'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
            $_SERVER['HTTP_CLIENT_IP'] = $_SERVER["HTTP_CF_CONNECTING_IP"];
        }
        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];

        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } else {
            $ip = $remote;
        }

        return $ip;
    }

    $user_ip = getUserIP() == "192.168.1.103" ? "103.81.85.224" : getUserIP();
    $details = json_decode(file_get_contents("http://ip-api.com/json/{$user_ip}"));
    if (isset($details->city) && isset($details->country)) {
        $location = "{$details->city}, {$details->country}";
    } else {
        $location = "Unknown";
    }
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

    $code = $id;
    $size = "Unknown";

    $sql = "INSERT INTO tracker (ref_code, ref_url, ip_address, location, screen_size, browser, OS, browser_user_agent) VALUES ('$code', '$ref_url', '$user_ip', '$location', '$size', '$browser', '$operating_system', '$user_agent')";
    // insert db
    $conn->query($sql);

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
            <div id="next_btn" onclick="openNewWindow('<?php echo $row['next_url'] ?>')" class="btn btn-primary disabled-button">Vui lòng đợi 6 giây...</div>
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
                if (s == 0 || s < 0) {
                    clearInterval(timer);
                    $("#next_btn").removeClass("disabled-button");
                    button.text("Bấm vào đây để tiếp tục!")
                }
            }, <?php echo $row['countdown_delay']; ?>);

            var width = window.screen.width;
            var height = window.screen.height;

            // Define the data to be sent to the PHP script
            const data = {
                id: "<?php echo $row['code'] ?>",
                size: width + 'x' + height
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