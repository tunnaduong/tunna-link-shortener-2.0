<?php
ob_start(); // Start output buffering

require_once $_SERVER['DOCUMENT_ROOT'] . '/serverconnect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/shortener_helpers.php';

$sql = "SELECT * FROM links WHERE code = '$id'";
$result = $conn->query($sql);

function callApi($id, $size, $ref)
{
    $url = "https://tunna.id.vn/api/tracker";
    $data = array(
        'id' => $id,
        'size' => $size,
        'ref' => $ref
    );

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ),
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) {
        // Handle error
    }

    return $result;
}

if ($result->num_rows > 0) {
    $row = mysqli_fetch_assoc($result);

    if ($row['redirect_type'] == 0) {
        $link = $row['next_url'];
        callApi($row['code'], "Unknown", "Unknown");
        header('Location: ' . $link);
        exit;
    }
?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php
        require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/head.php';
        $currentUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        ?>
        <title><?php echo isset($row['link_title']) ? $row['link_title'] : "Tunna Duong Link Shortener" ?></title>
        <meta property="og:title" content="<?php echo $row['link_title'] ?>" />
        <meta property="og:description" content="<?php echo isset($row['link_excerpt']) ? $row['link_excerpt'] : "Công cụ rút gọn link được tạo bởi Tunna Duong" ?>" />
        <meta property="og:type" content="website.url-shortener" />
        <meta property="og:url" content="<?php echo $currentUrl ?>" />
        <meta property="og:image" content="<?php echo isset($row['link_preview_url']) ? $row['link_preview_url'] : "/assets/images/link.jpg" ?>" />
    </head>

    <body>
        <center>
            <h1>Link Shortener</h1>
            <?php
            if (isset($_POST['g-recaptcha-response'])) {
                $captcha = $_POST['g-recaptcha-response'];
                $result_verify = recaptchaVerify($captcha) ?? false;
                if ($result_verify) {
                    header('Location: ' . $row['next_url']);
                } else {
                    echo "<div class='alert-danger'>Vui lòng xác minh bạn không phải là robot!</div>";
                }
            }

            // if redirect type is 2, then it's a recaptcha-protected link
            if ($row['redirect_type'] == 2) {
            ?>
                <form method='post' action="">
                    <div id="recaptcha" class="g-recaptcha" data-sitekey="6Ldga7MqAAAAAMaec8Hyk87vZksRcLUusHvYokX0" style="margin-bottom: 10px"></div>
                    <?= renderVerifyButton($row['wait_seconds'], $row['countdown_delay']) ?>
                </form>
                <script>
                    if (window.history.replaceState) {
                        window.history.replaceState(null, null, window.location.href);
                    }
                </script>
                <?php
                echo renderQRCodeSection();
                ?>
                <a href="#link_info" class="scroll-link">
                    <div class="btn">Xem thông tin chi tiết link</div>
                </a>
                <?php
                echo renderShareOptions();
                echo renderAds($row);
                $sql = "SELECT count(*) as total FROM tracker WHERE ref_code = '$id'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row2 = mysqli_fetch_assoc($result);
                    $view_count = $row2['total'];
                } else {
                    $view_count = 0;
                }
                echo renderLinkInfo($row, $view_count);
                ?>
                <script type="text/javascript">
                    atOptions = {
                        'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                        'format': 'iframe',
                        'height': 250,
                        'width': 300,
                        'params': {}
                    };
                </script>
                <script async="async" data-cfasync="false" type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
                <?php
                echo renderTags($row['tag']);
                ?>
                <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
                <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
                <?php
                echo renderFooter();
                ?>
                <script>
                    var width = window.screen.width;
                    var height = window.screen.height;
                    var referrer = document.referrer;
                    var data = {
                        id: "<?php echo $row['code'] ?>",
                        size: width + 'x' + height,
                        ref: referrer
                    };
                    $.ajax({
                        type: "POST",
                        url: "/api/tracker",
                        data: data,
                        success: function(response) {
                            console.log(response);
                        }
                    });
                    if (/^\?fbclid=/.test(location.search)) {
                        location.replace(location.href.replace(/\?fbclid.+/, ""));
                    }
                </script>
                <script src="/assets/js/script.js"></script>
                <script type="text/javascript">
                    var _captchaTries = 0;
                    var onloadCallback = function() {
                        _captchaTries++;
                        if (_captchaTries > 9)
                            return;
                        if ($('.g-recaptcha').length > 0) {
                            grecaptcha.render("recaptcha", {
                                sitekey: '6Ldga7MqAAAAAMaec8Hyk87vZksRcLUusHvYokX0',
                                callback: function() {
                                    console.log('recaptcha callback');
                                }
                            });
                            return;
                        }
                        window.setTimeout(recaptchaOnload, 1000);
                    };
                </script>
                <?php
            } else if (isset($row['password'])) {
                if (isset($_POST['password'])) {
                    if ($_POST['password'] == $row['password']) {
                        echo renderNextButton($row['next_url'], $row['wait_seconds'], $row['countdown_delay']);
                        echo renderQRCodeSection();
                ?>
                        <a href="#link_info" class="scroll-link">
                            <div class="btn">Xem thông tin chi tiết link</div>
                        </a>
                        <?php
                        echo renderShareOptions();
                        echo renderAds($row);
                        $sql = "SELECT count(*) as total FROM tracker WHERE ref_code = '$id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row2 = mysqli_fetch_assoc($result);
                            $view_count = $row2['total'];
                        } else {
                            $view_count = 0;
                        }
                        echo renderLinkInfo($row, $view_count);
                        ?>
                        <script type="text/javascript">
                            atOptions = {
                                'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                                'format': 'iframe',
                                'height': 250,
                                'width': 300,
                                'params': {}
                            };
                        </script>
                        <script async="async" data-cfasync="false" type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
                        <?php
                        echo renderTags($row['tag']);
                        ?>
                        <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
                        <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
                        <?php
                        echo renderFooter();
                        ?>
                        <script>
                            var width = window.screen.width;
                            var height = window.screen.height;
                            var referrer = document.referrer;
                            var data = {
                                id: "<?php echo $row['code'] ?>",
                                size: width + 'x' + height,
                                ref: referrer
                            };
                            $.ajax({
                                type: "POST",
                                url: "/api/tracker",
                                data: data,
                                success: function(response) {
                                    console.log(response);
                                }
                            });
                            if (/^\?fbclid=/.test(location.search)) {
                                location.replace(location.href.replace(/\?fbclid.+/, ""));
                            }
                        </script>
                        <script src="/assets/js/script.js"></script>
                    <?php
                    } else {
                    ?>
                        <h4>Cần có mật khẩu để xem liên kết này</h4>
                        <div class='alert-danger'>Mật khẩu không chính xác!</div>
                        <form method='post' id="pw-form" action="">
                            <div class='form-group'>
                                <input class="pw" type='password' placeholder="Nhập mật khẩu..." name='password' style="max-width: 320px;width: 100%;box-sizing: border-box;">
                            </div>
                            <button type='submit' class='btn btn-primary' style="min-width: 320px;">Xác nhận</button>
                        </form>
                        <script>
                            if (window.history.replaceState) {
                                window.history.replaceState(null, null, window.location.href);
                            }
                        </script>
                        <?php
                        echo renderQRCodeSection();
                        ?>
                        <a href="#link_info" class="scroll-link">
                            <div class="btn">Xem thông tin chi tiết link</div>
                        </a>
                        <?php
                        echo renderShareOptions();
                        echo renderAds($row);
                        $sql = "SELECT count(*) as total FROM tracker WHERE ref_code = '$id'";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            $row2 = mysqli_fetch_assoc($result);
                            $view_count = $row2['total'];
                        } else {
                            $view_count = 0;
                        }
                        echo renderLinkInfo($row, $view_count);
                        ?>
                        <script type="text/javascript">
                            atOptions = {
                                'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                                'format': 'iframe',
                                'height': 250,
                                'width': 300,
                                'params': {}
                            };
                        </script>
                        <script async="async" data-cfasync="false" type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
                        <?php
                        echo renderTags($row['tag']);
                        ?>
                        <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
                        <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
                        <?php
                        echo renderFooter();
                        ?>
                        <script>
                            var width = window.screen.width;
                            var height = window.screen.height;
                            var referrer = document.referrer;
                            var data = {
                                id: "<?php echo $row['code'] ?>",
                                size: width + 'x' + height,
                                ref: referrer
                            };
                            $.ajax({
                                type: "POST",
                                url: "/api/tracker",
                                data: data,
                                success: function(response) {
                                    console.log(response);
                                }
                            });
                            if (/^\?fbclid=/.test(location.search)) {
                                location.replace(location.href.replace(/\?fbclid.+/, ""));
                            }
                        </script>
                        <script src="/assets/js/script.js"></script>
                    <?php
                    }
                } else {
                    ?>
                    <h4>Cần có mật khẩu để xem liên kết này</h4>
                    <form method='post' action="">
                        <div class='form-group'>
                            <input class="pw" type='password' placeholder="Nhập mật khẩu..." name='password' style="max-width: 320px;width: 100%;box-sizing: border-box;">
                        </div>
                        <button type='submit' class='btn btn-primary' style="min-width: 320px;">Xác nhận</button>
                    </form>
                    <?php
                    echo renderQRCodeSection();
                    ?>
                    <a href="#link_info" class="scroll-link">
                        <div class="btn">Xem thông tin chi tiết link</div>
                    </a>
                    <?php
                    echo renderShareOptions();
                    echo renderAds($row);
                    $sql = "SELECT count(*) as total FROM tracker WHERE ref_code = '$id'";
                    $result = $conn->query($sql);

                    if ($result->num_rows > 0) {
                        $row2 = mysqli_fetch_assoc($result);
                        $view_count = $row2['total'];
                    } else {
                        $view_count = 0;
                    }
                    echo renderLinkInfo($row, $view_count);
                    ?>
                    <script type="text/javascript">
                        atOptions = {
                            'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                            'format': 'iframe',
                            'height': 250,
                            'width': 300,
                            'params': {}
                        };
                    </script>
                    <script async="async" data-cfasync="false" type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
                    <?php
                    echo renderTags($row['tag']);
                    ?>
                    <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
                    <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
                    <?php
                    echo renderFooter();
                    ?>
                    <script>
                        var width = window.screen.width;
                        var height = window.screen.height;
                        var referrer = document.referrer;
                        var data = {
                            id: "<?php echo $row['code'] ?>",
                            size: width + 'x' + height,
                            ref: referrer
                        };
                        $.ajax({
                            type: "POST",
                            url: "/api/tracker",
                            data: data,
                            success: function(response) {
                                console.log(response);
                            }
                        });
                        if (/^\?fbclid=/.test(location.search)) {
                            location.replace(location.href.replace(/\?fbclid.+/, ""));
                        }
                    </script>
                    <script src="/assets/js/script.js"></script>
                <?php
                }
            } else {
                echo renderNextButton($row['next_url'], $row['wait_seconds'], $row['countdown_delay']);
                echo renderQRCodeSection();
                ?>
                <a href="#link_info" class="scroll-link">
                    <div class="btn">Xem thông tin chi tiết link</div>
                </a>
                <?php
                echo renderShareOptions();
                echo renderAds($row);
                $sql = "SELECT count(*) as total FROM tracker WHERE ref_code = '$id'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row2 = mysqli_fetch_assoc($result);
                    $view_count = $row2['total'];
                } else {
                    $view_count = 0;
                }
                echo renderLinkInfo($row, $view_count);
                ?>
                <script type="text/javascript">
                    atOptions = {
                        'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                        'format': 'iframe',
                        'height': 250,
                        'width': 300,
                        'params': {}
                    };
                </script>
                <script async="async" data-cfasync="false" type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
                <?php
                echo renderTags($row['tag']);
                ?>
                <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
                <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
                <?php
                echo renderFooter();
                ?>
        </center>
        <script>
            var width = window.screen.width;
            var height = window.screen.height;
            var referrer = document.referrer;
            var data = {
                id: "<?php echo $row['code'] ?>",
                size: width + 'x' + height,
                ref: referrer
            };
            $.ajax({
                type: "POST",
                url: "/api/tracker",
                data: data,
                success: function(response) {
                    console.log(response);
                }
            });
            if (/^\?fbclid=/.test(location.search)) {
                location.replace(location.href.replace(/\?fbclid.+/, ""));
            }
        </script>
        <script src="/assets/js/script.js"></script>
    <?php
            }
        } else {
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/head.php';
            $currentUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        ?>
        <title><?php echo isset($row['link_title']) ? $row['link_title'] : "Tunna Duong Link Shortener" ?></title>
        <meta property="og:title" content="<?php echo $row['link_title'] ?>" />
        <meta property="og:description" content="<?php echo isset($row['link_excerpt']) ? $row['link_excerpt'] : "Công cụ rút gọn link được tạo bởi Tunna Duong" ?>" />
        <meta property="og:type" content="website.url-shortener" />
        <meta property="og:url" content="<?php echo $currentUrl ?>" />
        <meta property="og:image" content="<?php echo isset($row['link_preview_url']) ? $row['link_preview_url'] : "/assets/images/link.jpg" ?>" />
    </head>

    <body>
        <center>
            <h1>Link Shortener</h1>
            <div>
                <img src="/assets/images/404.png" class="_404 dude" alt="404 Not Found">
            </div>
            <script type="text/javascript">
                atOptions = {
                    'key': '2af190ba44f51f05b0f68a0224e3d5fc',
                    'format': 'iframe',
                    'height': 250,
                    'width': 300,
                    'params': {}
                };
            </script>
            <script async="async" data-cfasync="false" type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
            <script async="async" data-cfasync="false" src="//pl25523691.profitablecpmrate.com/e19b2044d36d5ec26b29ac25e2e560a9/invoke.js"></script>
            <div id="container-e19b2044d36d5ec26b29ac25e2e560a9" style="color: white; max-width: 600px"></div>
            <?= renderFooter() ?>
        </center>
        <?php
            require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/footer.php';
        ?>
    </body>

    </html>
<?php
        }
        $conn->close();
        ob_end_flush();
?>