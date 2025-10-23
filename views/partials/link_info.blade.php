@php
$createdAt = $link->getCreatedAt()->format('Y-m-d H:i:s');
$passwordInfo = $link->hasPassword() ? 'Có' : 'Không có';
@endphp
<h3 id="link_info">Thông tin liên kết</h3>
<table class="table" style="margin-bottom: 20px;">
  <tbody>
    <tr>
      <td>ID</td>
      <td>{{ $link->getCode() }}</td>
    </tr>
    <tr>
      <td>Tiêu đề</td>
      <td>{{ $link->getLinkTitle() ?: 'N/A' }}</td>
    </tr>
    <tr>
      <td>Mô tả</td>
      <td>{{ $link->getLinkExcerpt() ?: 'N/A' }}</td>
    </tr>
    <tr>
      <td>Mật khẩu</td>
      <td>{{ $passwordInfo }}</td>
    </tr>
    <tr>
      <td>Lượt truy cập</td>
      <td>{{ $visit_count }}</td>
    </tr>
    <tr>
      <td>Thời gian tạo</td>
      <td>
        <script>document.write(moment('{{ $createdAt }}').locale('vi').fromNow());</script>
      </td>
    </tr>
  </tbody>
</table>