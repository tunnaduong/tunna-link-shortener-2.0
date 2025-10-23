@php
    $adsUrl = !empty($link->getAdsClickUrl()) ? $link->getAdsClickUrl() : 'https://zalo.me/0365520031';
    $adsImg = !empty($link->getAdsImgUrl()) ? $link->getAdsImgUrl() : '/assets/images/1.gif';
    $promotedBy = !empty($link->getAdsPromotedBy()) ? $link->getAdsPromotedBy() : 'tunnaAds';
@endphp
<a href="{{ $adsUrl }}" id="ads">
    <img class="ads" src="{{ $adsImg }}" alt="Ads" width="550">
</a>
<div>
    <sup style="color: lightgray;">Quảng cáo tài trợ bởi: {{ $promotedBy }}</sup>
</div>
