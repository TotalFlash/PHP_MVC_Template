<?php

namespace viewModel;

use HTML_Template_IT;
use JetBrains\PhpStorm\NoReturn;
use model\Navigation;

class AbstractViewModel
{
  protected string $title = '';
  protected string $html = '';

  protected bool $parseMenu = true;
  protected bool $parseFooter = true;

  /**
   * Creates an instance of HTML_Template_IT
   *
   * @return HTML_Template_IT
   */
  protected function getTemplateEngine(): HTML_Template_IT
  {
    return new HTML_Template_IT( VIEW_PATH . self::getClassName() );
  }

  /**
   * Returns the class name of the called function in lowercase
   *
   * @return string
   */
  private static function getClassName (): string
  {
    $className = get_called_class();
    $className = str_replace( 'viewModel\\', '', $className );
    $className = str_replace( 'View', '', $className );
    return strtolower( $className );
  }

  #[NoReturn]
  public function renderFullHTML (): void
  {
    $it = new HTML_Template_IT( VIEW_PATH );;
    $it->loadTemplatefile( 'layout.html' );
    $placeholder = [
      'title' => "{$this->title}",
      'designCss' => 'assets/css/design.css?' . filemtime( CSS_PATH . 'design.css' ),
      'scriptJS' => 'assets/js/script.js?' . filemtime( JAVA_SCRIPT_PATH . 'script.js' ),
      'content' => $this->html,
    ];

    if ($this->parseMenu)
    {
      $placeholder[ 'menu' ] = Navigation::getMenu();
    }

    if ($this->parseFooter)
    {
      $placeholder[ 'footer' ] = Navigation::getFooter();
    }

    $it->setVariable( $placeholder );
    $it->parseCurrentBlock();
    echo $it->get();
    exit( 0 );
  }
}