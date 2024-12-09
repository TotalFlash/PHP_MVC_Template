<?php

namespace controller;

use model\User;
use viewModel\SharedViewModel;

class SharedController extends AbstractController
{
  public function home(): void
  {
    $userCount = count(User::getAllUsers());
    $sharedViewModel = new SharedViewModel();
    $sharedViewModel->renderHomeView($userCount);
  }
}