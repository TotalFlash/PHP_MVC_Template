<?php

namespace viewModel;

use DateTime;

class UserView extends AbstractViewModel
{
  /**
   * Creates the HTML for the user list
   *
   * @param array $users - Array with all users
   *
   * @return UserView - Returns itself
   */
  public function renderList (array $users): self
  {
    $this->title = 'Nutzerliste';
    ob_start();
    extract($users);
    include VIEW_PATH . 'user/list.php';
    $this->html = ob_get_clean();
    return $this;
  }
}