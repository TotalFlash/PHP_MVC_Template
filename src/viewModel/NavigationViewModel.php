<?php

namespace viewModel;

class NavigationViewModel extends AbstractViewModel
{
  public static function renderNavigation (): string
  {
    $pageParameter = [
      'homeLink' => '?c=Pages&a=home',
      'userList' => '?c=User&a=list'
    ];

    ob_start();
    include VIEW_PATH . "navigation/menu.php";
    extract( $pageParameter );
    return ob_get_clean();
  }

  public static function renderFooter (): string
  {
    $pageParameter = [
      'footerText' => 'This is a footer'
    ];

    ob_start();
    extract( $pageParameter );
    include VIEW_PATH . "navigation/footer.php";
    return ob_get_clean();
  }
}