<?php

namespace model;

abstract class Navigation
{

  public static function getMenu (): string
  {
    // HTML Dateien ohne lögik könne und sollten einfach so geladen und ausgegeben werden
    return file_get_contents( VIEW_PATH . 'navigation/menu.html' );
  }

  public static function getFooter (): string
  {
    // HTML Dateien ohne lögik könne und sollten einfach so geladen und ausgegeben werden
    return file_get_contents( VIEW_PATH . 'navigation/footer.html' );
  }
}