<?php

namespace viewModel;

use DateTime;

class UserView extends AbstractViewModel
{
  /**
   * Creates the HTML for the user list
   *
   * @param array $users - Array with all users
   *
   * @return UserView - Returns itself
   * @throws \DateMalformedStringException
   */
  public function list (array $users): self
  {
    $this->title = 'Nutzerliste';
    $it = $this->getTemplateEngine();
    $it->loadTemplatefile( 'list.html' );

    foreach ($users as $user)
    {
      $it->setCurrentBlock( 'userListEntry' );
      $it->setVariable( [
        'id' => $user[ 'id' ],
        'name' => $user[ 'name' ],
        'createdAt' => ( new DateTime( $user[ 'createdAt' ] ) )->format( 'd.m.Y H:i:s' ),
      ] );
      $it->parse( 'userListEntry' );
    }
    // Setzt global das fertige geparste HTML für den Content. D.h. im besten Fall alles, was dynamisch generiert wurde.
    // Die Variable wird dann in der renderHTML() benutzt und der Content in die head.php geladen
    $this->html = $it->get();
    return $this;
  }
}