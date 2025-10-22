<?php
function renderQRCodeSection()
{
  return <<<HTML
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
    HTML;
}

function renderLinkInfo($row, $visitCount = 0)
{
  $createdAt = $row['created_at'] ?? 'N/A';
  $passwordInfo = $row['password'] ? 'Có' : 'Không có';

  return <<<HTML
    <h3 id="link_info">Thông tin liên kết</h3>
    <table class="table" style="margin-bottom: 20px;">
        <tbody>
            <tr><td>ID</td><td>{$row['code']}</td></tr>
            <tr><td>Tiêu đề</td><td>{$row['link_title']}</td></tr>
            <tr><td>Mô tả</td><td>{$row['link_excerpt']}</td></tr>
            <tr><td>Mật khẩu</td><td>{$passwordInfo}</td></tr>
            <tr><td>Lượt truy cập</td><td>{$visitCount}</td></tr>
            <tr><td>Thời gian tạo</td><td><script>document.write(moment('{$createdAt}').locale('vi').fromNow());</script></td></tr>
        </tbody>
    </table>
    HTML;
}

function renderTags($tags)
{
  // Nếu danh sách thẻ không có dữ liệu, trả về thông báo mặc định
  if (empty($tags)) {
    return '<span class="badge">Không có thẻ</span>';
  }

  // Phân tách chuỗi thành mảng (nếu lưu trữ thẻ dưới dạng chuỗi)
  $tagsArray = explode(',', $tags);

  // Tạo HTML cho từng thẻ
  $html = '';
  foreach ($tagsArray as $tag) {
    $html .= "<span class='badge'>" . htmlspecialchars(trim($tag)) . "</span> ";
  }

  return "<p class='tag'>Thẻ: " . $html . "</p>";
}

function renderFooter()
{
  return <<<HTML
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
    HTML;
}

function renderAds($row)
{
  $adsUrl = $row['ads_click_url'] ?? "https://zalo.me/0365520031";
  $adsImg = $row['ads_img_url'] ?? "/assets/images/demo.gif";
  $promotedBy = $row['ads_promoted_by'] ?? "tunnaAds";

  return <<<HTML
    <a href="{$adsUrl}" id="ads">
        <img class="ads" src="{$adsImg}" alt="Ads" width="550">
    </a>
    <div>
        <sup style="color: lightgray;">Quảng cáo tài trợ bởi: {$promotedBy}</sup>
    </div>
    HTML;
}

function renderShareOptions()
{
  return <<<HTML
    <h2><span><i class="fas fa-share"></i> Chia sẻ</span></h2>
    <div class="social">
        <i onclick="fbShare()" class="fab fa-facebook"></i>
        <i onclick="twitterShare()" class="fab fa-twitter-square"></i>
        <i onclick="copyPageUrl()" class="fas fa-copy"></i>
    </div>
    HTML;
}

function renderNextButton($nextUrl, $waitSeconds = 10, $countdownDelay = 1000)
{
  return <<<HTML
    <!-- onclick="openNewWindow(`{$nextUrl}`)" -->
    <a href="#ads" id="next_btn" class="scroll-link btn btn-primary disabled-button">
        Vui lòng đợi {$waitSeconds} giây...
    </a>
    <script type="text/javascript">
        var remainingTime = {$waitSeconds};
        var timer = setInterval(() => {
            remainingTime--;
            const button = document.getElementById('next_btn');
            button.innerText = "Vui lòng đợi " + remainingTime + " giây...";
            if (remainingTime <= 0) {
                clearInterval(timer);
                button.classList.remove('disabled-button');
                button.innerText = "Bấm vào đây để tiếp tục!";
                // button.onclick = () => window.location.href = `{$nextUrl}`;
            }
        }, {$countdownDelay});
    </script>
    HTML;
}

function recaptchaVerify($recaptchaResponse)
{
  $secretKey = '6Ldga7MqAAAAANQwYsiNr6DJw70CvNqpsZPjLthL'; // Replace with your secret key
  $verifyUrl = 'https://www.google.com/recaptcha/api/siteverify';
  $response = file_get_contents($verifyUrl . '?secret=' . $secretKey . '&response=' . $recaptchaResponse . '&remoteip=' . $_SERVER['REMOTE_ADDR']);
  $responseKeys = json_decode($response, true);

  if ($responseKeys['success']) {
    // Validation passed
    return true;
  } else {
    // Validation failed
    return false;
  }
}

function renderVerifyButton($waitSeconds = 10, $countdownDelay = 1000)
{
  return <<<HTML
    <button type="submit" id="next_btn" class="btn btn-primary disabled-button" style="min-width: 304px;">
        Vui lòng đợi {$waitSeconds} giây...
    </button>
    <script type="text/javascript">
        var remainingTime = {$waitSeconds};
        var timer = setInterval(() => {
            remainingTime--;
            const button = document.getElementById('next_btn');
            button.innerText = "Vui lòng đợi " + remainingTime + " giây...";
            if (remainingTime <= 0) {
                clearInterval(timer);
                button.classList.remove('disabled-button');
                button.innerText = "Xác minh và tiếp tục!";
            }
        }, {$countdownDelay});
    </script>
    HTML;
}

function renderContinueButton($nextUrl)
{
  return <<<HTML
    <a href="#ads" id="next_btn" class="scroll-link btn btn-primary">
        Liên kết của bạn đã sẵn sàng!
    </a>
    HTML;
}


function renderVisitButton($nextUrl, $waitSeconds = 10, $countdownDelay = 1000)
{
  return <<<HTML
    <div id="next_btn2" onclick="openNewWindow(`{$nextUrl}`)" style="margin-top: 10px" class="btn disabled-button btn-primary">
        Vui lòng đợi {$waitSeconds} giây...
    </div>
    <script type="text/javascript">
        var remainingTime2 = {$waitSeconds};
        var timer2 = setInterval(() => {
            remainingTime2--;
            const button2 = document.getElementById('next_btn2');
            button2.innerText = "Vui lòng đợi " + remainingTime2 + " giây...";
            if (remainingTime2 <= 0) {
                clearInterval(timer2);
                button2.classList.remove('disabled-button');
                button2.innerText = "Mở liên kết!";
            }
        }, {$countdownDelay});
    </script>
    HTML;
}

function renderVisitButton2($nextUrl)
{
  return <<<HTML
    <div onclick="openNewWindow(`{$nextUrl}`)" style="margin-top: 10px" id="next_btn2" class="btn btn-primary">
        Mở liên kết!
    </div>
    HTML;
}
