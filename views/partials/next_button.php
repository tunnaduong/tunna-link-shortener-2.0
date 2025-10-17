<?= $this->renderPartial('next_button_template', [
  'link' => $link,
  'wait_seconds' => $link->getWaitSeconds(),
  'countdown_delay' => $link->getCountdownDelay(),
  'next_url' => $link->getNextUrl()
]) ?>