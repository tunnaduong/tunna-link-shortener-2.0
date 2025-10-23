@extends('layouts.main')

@section('content')
<center>
  <h1>Link Shortener</h1>
  @include('partials.continue_button', ['link' => $link])
  @include('partials.qr_code_section')
  <a href="#link_info" class="scroll-link">
    <div class="btn">Xem thông tin chi tiết link</div>
  </a>
  @include('partials.share_options')
  @include('partials.ads', ['link' => $link])
  @include('partials.visit_button_immediate', ['link' => $link])
  @include('partials.link_info', ['link' => $link, 'visit_count' => $visit_count])
  @include('partials.ads_scripts')
  @include('partials.tags', ['link' => $link])
  @include('partials.footer')
  @include('partials.tracking_script', ['link' => $link])
</center>
@endsection