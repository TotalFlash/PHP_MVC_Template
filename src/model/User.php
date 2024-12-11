<?php

namespace model;

use DateTime;

class User extends AbstractModel
{
  protected string $name;
  protected DateTime $createdAt;

  /**
   * Creates User by id and data from database
   *
   * @return User|null
   * @throws \DateMalformedStringException
   */
  public function __construct (?int $id = null)
  {
    if (!is_null( $id ))
    {
      $user = self::read( 'id = :id', [ ':id' => $id ], '*', 1 );
      $this->id = $user[ 'id' ];
      $this->name = $user[ 'name' ];
      $this->createdAt = new DateTime( $user[ 'createdAt' ] );
    }

    return null;
  }

  /**
   * Gets all users from database
   *
   * @return array
   */
  public static function getAllUsers (): array
  {
    return self::read('', [], 'id, name');
  }

  public static function create (): int
  {
    // validierung
    // passwort hash und salt
    //


    return 1;
  }

  public function getName (): string
  {
    return $this->name;
  }

  public function setName (string $name): void
  {
    $this->name = $name;
  }

  public function getCreatedAt (): DateTime
  {
    return $this->createdAt;
  }

  public function setCreatedAt (DateTime $createdAt): void
  {
    $this->createdAt = $createdAt;
  }


}