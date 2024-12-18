<?php

namespace viewModel;

use DateTime;
use model\User;

class UserViewModel extends AbstractViewModel
{
  /**
   * Creates the HTML for the user list
   *
   * @param array $users - Array with all users
   *
   * @return UserViewModel - Returns itself
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

  public function renderEditView (User $user): self
  {
    $this->title = 'Nutzer ' . is_null($user) ? 'anlegen' : 'bearbeiten';
    ob_start();
    $parameter = [
      'action' => is_null($user) ? 'create' : "update&userId={$user->getId()}",
      'buttonText' => is_null($user) ? 'anlegen' : 'bearbeiten',
      'userName' => $user?->getName() ?? '',
      'userCreatedAt' => $user?->getCreatedAt()?->format('d.m.Y H:i:s') ?? '',
    ];
    extract($parameter);
    include VIEW_PATH . 'user/edit.php';
    $this->html = ob_get_clean();
    return $this;
  }

  public function renderLogin (string $errors = ''): self
  {
    ob_start();
    include VIEW_PATH . 'user/login.php';
    $this->html = ob_get_clean();
    return $this;
  }
}