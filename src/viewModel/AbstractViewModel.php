<?php

namespace viewModel;

class AbstractViewModel
{
  protected string $title = '';
  protected string $html = '';

  protected bool $parseMenu = true;
  protected bool $parseFooter = true;

  public function renderNav(): void
  {
    if(!$this->parseMenu)
    {
      return;
    }

    $filePath = VIEW_PATH . 'navigation/menu.php';
    include $filePath;
  }

  public function renderHead(): void
  {
    $title = $this->title;
    $filePath = VIEW_PATH . 'navigation/head.php';
    include $filePath;
  }
}