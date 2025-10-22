<?php

namespace App\Controllers;

use App\Services\ViewRenderer;

class HomeController
{
  private $viewRenderer;

  public function __construct(ViewRenderer $viewRenderer)
  {
    $this->viewRenderer = $viewRenderer;
  }

  public function index(): void
  {
    $this->viewRenderer->render('index', [
      'title' => 'Tunna Duong Link Shortener',
      'description' => 'Công cụ rút gọn link được tạo bởi Tunna Duong'
    ]);
  }
}
