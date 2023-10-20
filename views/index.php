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
        <img src="/assets/images/dude.jpg" class="_404 dude" alt="404 Not Found">
        <h2>Quay lại trang trước đi. Ở đây không có gì đâu ^^</h2>
        <div class="ads">
            <div id="awn-z7610822"></div>
        </div>
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