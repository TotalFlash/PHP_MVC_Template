<?php

namespace controller;

use JetBrains\PhpStorm\NoReturn;
use model\User;
use viewModel\UserView;

class UserController extends AbstractController
{

  #[NoReturn]
  public function list(): void
  {
    $allUsers = User::getAllUsers();
    $viewModel = new UserView();
    $viewModel
      // Parst die Nutzerliste
      ->renderList($allUsers)
      // Fügt die Nutzerliste in die Layoutdatei ein und parst den rest drumherum
      ->renderFullHTML();
  }
}