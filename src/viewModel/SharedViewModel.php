<?php

namespace viewModel;


class SharedViewModel extends AbstractViewModel
{
  public function renderHomeView(int $userCount): void
  {
    $filePath = VIEW_PATH . "shared/home.php";
    // Nav
    $this->renderHead();
    $this->renderNav();
    // Content
    include $filePath;
  }
}