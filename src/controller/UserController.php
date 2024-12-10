<?php

namespace controller;

use model\User;
use viewModel\UserViewModel;

class UserController extends AbstractController
{
  public function list(): void
  {
    $allUsers = User::getAllUsers();
    $userViewModel = new UserViewModel();
    $userViewModel
      ->renderList($allUsers)
      ->renderFullHTML();
  }
}