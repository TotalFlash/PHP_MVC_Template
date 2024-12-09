<?php

namespace viewModel;

use JetBrains\PhpStorm\NoReturn;

class AbstractViewModel
{
  protected string $title = '';
  protected string $html = '';

  protected bool $parseMenu = true;
  protected bool $parseFooter = true;

  #[NoReturn]
  public function renderFullHTML(): void
  {
    ob_start();
    $nav = '';
    $footer = '';
    $content = $this->html;

    if($this->parseMenu)
    {
      $nav = NavigationViewModel::renderNavigation();
    }

    if($this->parseFooter)
    {
      $footer = NavigationViewModel::renderFooter();
    }

    include VIEW_PATH . 'layout.php';
    echo ob_get_clean();
    exit(0);
  }
}