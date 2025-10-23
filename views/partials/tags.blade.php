@php
$tags = $link->getTag();
@endphp
@if(empty($tags))
<span class="badge">Không có thẻ</span>
@else
@php
$tagsArray = explode(',', $tags);
@endphp
<p class='tag'>Thẻ:
  @foreach($tagsArray as $tag)
  <span class='badge'>{{ trim($tag) }}</span>
  @endforeach
</p>
@endif