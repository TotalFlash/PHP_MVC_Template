<?php

namespace controller;

use model\User;
use viewModel\SharedViewModel;

class SharedController extends AbstractController
{
  public function home(): void
  {
    $activeUsers = count(User::getAllUsers());
    $sharedViewModel = new SharedViewModel();
    $sharedViewModel
      ->renderHome($activeUsers)
      ->renderFullHTML();
  }
}