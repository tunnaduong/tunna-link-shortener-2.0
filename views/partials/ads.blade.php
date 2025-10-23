@php
$adsUrl = $link->getAdsClickUrl() ?? "https://zalo.me/0365520031";
$adsImg = $link->getAdsImgUrl() ?? "/assets/images/1.gif";
$promotedBy = $link->getAdsPromotedBy() ?? "tunnaAds";
@endphp
<a href="{{ $adsUrl }}" id="ads">
  <img class="ads" src="{{ $adsImg }}" alt="Ads" width="550">
</a>
<div>
  <sup style="color: lightgray;">Quảng cáo tài trợ bởi: {{ $promotedBy }}</sup>
</div>