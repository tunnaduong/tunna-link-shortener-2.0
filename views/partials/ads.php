<?php
$adsUrl = $link->getAdsClickUrl() ?? "https://zalo.me/0365520031";
$adsImg = $link->getAdsImgUrl() ?? "/assets/images/1.gif";
$promotedBy = $link->getAdsPromotedBy() ?? "tunnaAds";
?>
<a href="<?= htmlspecialchars($adsUrl) ?>" id="ads">
  <img class="ads" src="<?= htmlspecialchars($adsImg) ?>" alt="Ads" width="550">
</a>
<div>
  <sup style="color: lightgray;">Quảng cáo tài trợ bởi: <?= htmlspecialchars($promotedBy) ?></sup>
</div>