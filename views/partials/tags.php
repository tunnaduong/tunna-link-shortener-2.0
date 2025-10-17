<?php
$tags = $link->getTag();
if (empty($tags)) {
  echo '<span class="badge">Không có thẻ</span>';
} else {
  $tagsArray = explode(',', $tags);
  $html = '';
  foreach ($tagsArray as $tag) {
    $html .= "<span class='badge'>" . htmlspecialchars(trim($tag)) . "</span> ";
  }
  echo "<p class='tag'>Thẻ: " . $html . "</p>";
}
?>