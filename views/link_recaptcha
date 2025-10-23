@extends('layouts.main')

@section('content')
<center>
  <h1>Link Shortener</h1>
  <h4>Xác minh bạn không phải là robot để tiếp tục...</h4>
  @if(isset($error))
  <div class='alert-danger'>{{ $error }}</div>
  @endif
  <form method='post' action="">
    <div id="recaptcha" class="g-recaptcha" data-sitekey="{{ $recaptcha_site_key }}" style="margin-bottom: 8px"></div>
    @include('partials.verify_button', ['link' => $link])
  </form>
  @include('partials.qr_code_section')
  <a href="#link_info" class="scroll-link">
    <div class="btn">Xem thông tin chi tiết link</div>
  </a>
  @include('partials.share_options')
  @include('partials.ads', ['link' => $link])
  @include('partials.link_info', ['link' => $link, 'visit_count' => $visit_count])
  @include('partials.ads_scripts')
  @include('partials.tags', ['link' => $link])
  @include('partials.footer')
  @include('partials.tracking_script', ['link' => $link])
  @include('partials.recaptcha_script', ['recaptcha_site_key' => $recaptcha_site_key])
</center>
@endsection