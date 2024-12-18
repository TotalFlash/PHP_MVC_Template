<?php

namespace controller;

use model\User;
use viewModel\UserViewModel;

class UserController extends AbstractController
{
  public function list(): void
  {
    User::hasPermission(1);
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

  public function login(): void
  {
    $errors = '';
    if(!empty($_POST['username']) && !empty($_POST['password']))
    {
      if(!User::login($_POST['username'], $_POST['password']))
      {
        die('Passwort falsch');
      } else {
        header('Location: ?c=Shared&a=home');
        exit(0);
      }
    }

    $userViewModel = new UserViewModel();
    $userViewModel
      ->renderLogin($errors)
      ->renderFullHTML();
  }

  public function logout(): void
  {
    session_destroy();
    header('Location: ?c=Shared&a=home');
  }

}