<?php

namespace controller;

use controller\AbstractController;
use model\User;
use viewModel\UserView;

class UserController extends AbstractController
{
  public function list(): void
  {
    $allUsers = User::getAllUsers();
    $userViewModel = new UserView();
    $userViewModel
      ->renderList($allUsers)
      ->renderFullHTML();
  }
}