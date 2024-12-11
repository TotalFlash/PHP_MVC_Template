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

  public function edit(): void
  {
    // Permission Check

    $userId = $_GET['userId'] ?? null;
    $user = new User($userId);

    $userViewModel = new UserViewModel();
    $userViewModel
      ->renderEditView($user)
      ->renderFullHTML();
  }
}