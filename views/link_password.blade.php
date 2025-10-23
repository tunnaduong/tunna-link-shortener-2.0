@extends('layouts.main')

@section('content')
<center>
  <h1>Link Shortener</h1>
  <h4>Cần có mật khẩu để xem liên kết này</h4>
  @if(isset($error))
  <div class='alert-danger'>{{ $error }}</div>
  @endif
  <form method='post' action="">
    <div class='form-group'>
      <input class="pw" type='password' placeholder="Nhập mật khẩu..." name='password'
        style="max-width: 304px;width: 100%;box-sizing: border-box;">
    </div>
    <button type='submit' class='btn btn-primary' style="min-width: 304px;">Xác nhận</button>
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
</center>
@endsection