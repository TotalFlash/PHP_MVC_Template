<?php

namespace controller;

use model\User;
use viewModel\UserView;

class UserController extends AbstractController
{

  public function list(): void
  {
    $allUsers = user::getAllUsers();
    $viewModel = new UserView();
    $viewModel
      // Parst die Nutzerliste
      ->list($allUsers)
      // Fügt die Nutzerliste in die Layoutdatei ein und parst den rest drumherum
      ->renderFullHTML();
  }
}