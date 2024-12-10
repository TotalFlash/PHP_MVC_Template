<?php

namespace viewModel;

use viewModel\AbstractViewModel;

class SharedViewModel extends AbstractViewModel
{
  /**
   * Creates the HTML for the home page
   *
   * @param int $userCount - Count of all active users
   *
   * @return SharedViewModel - Returns itself
   */
  public function renderHome (int $userCount): self
  {
    $this->title = 'Home';
    ob_start();
    include VIEW_PATH . 'shared/home.php';
    $this->html = ob_get_clean();
    return $this;
  }
}