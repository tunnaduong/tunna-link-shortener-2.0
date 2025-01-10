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
            <img src="/assets/images/404.png" class="_404" alt="404 Not Found">
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