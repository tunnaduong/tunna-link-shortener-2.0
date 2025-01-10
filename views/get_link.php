<?php
// To call this page, in the browser type:
// http://localhost/$id
require_once $_SERVER['DOCUMENT_ROOT'] . '/serverconnect.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/includes/shortener_helpers.php';

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
            <?php
            if (isset($row['password'])) {
                if (isset($_POST['password'])) {
                    if ($_POST['password'] == $row['password']) {
                        // Password is correct
                        echo renderNextButton($row['next_url'], $row['wait_seconds'], $row['countdown_delay']);
                        echo renderQRCodeSection();
            ?>
                        <a href="#link_info" class="scroll-link">
                            <div onclick class="btn">Xem thông tin chi tiết link</div>
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
                        <script type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
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
                        <script src="/assets/js/script.js"></script>
                    <?php
                    } else {
                        // Password is incorrect
                    ?>
                        <h4>Cần có mật khẩu để xem liên kết này</h4>
                        <div class='alert-danger'>Mật khẩu không chính xác!</div>
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
                            <div onclick class="btn">Xem thông tin chi tiết link</div>
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
                        <script type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
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
                        <script src="/assets/js/script.js"></script>
                    <?php
                    }
                } else {
                    // Show the password form
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
                        <div onclick class="btn">Xem thông tin chi tiết link</div>
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
                    <script type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
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
                    <script src="/assets/js/script.js"></script>
                <?php
                }
            } else {
                // No password is set
                echo renderNextButton($row['next_url'], $row['wait_seconds'], $row['countdown_delay']);
                echo renderQRCodeSection();
                ?>
                <a href="#link_info" class="scroll-link">
                    <div onclick class="btn">Xem thông tin chi tiết link</div>
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
                <script type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
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
        <script src="/assets/js/script.js"></script>
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
        <script type="text/javascript" src="//www.highperformanceformat.com/2af190ba44f51f05b0f68a0224e3d5fc/invoke.js"></script>
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

?>