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
    return self::read( '', [], 'id, name' );
  }

  public static function create (): int
  {
    // validierung
    // passwort hash und salt
    //

    return 1;
  }

  public static function login (string $username, string $password): bool
  {
    $user = User::read( 'name = :username', [ ':username' => $username ], '*', 1);
    if (empty( $user ))
    {
      return false;
    }

    if ($password !== $user[ 'password' ])
    {
      return false;
    }

    $_SESSION[ 'userId' ] = $user[ 'id' ];
    $_SESSION[ 'name' ] = $user[ 'name' ];
    $_SESSION[ 'loggedIn' ] = true;
    return true;
  }

  public static function setNewSessionData (): void
  {
    $user = self::read( 'id = :id', [ ':id' => $_SESSION[ 'userId' ] ], '*', 1);
    $_SESSION[ 'name' ] = $user[ 'name' ];
    $_SESSION[ 'createdAt' ] = $user[ 'createdAt' ];
  }

  public static function hasPermission (int $permissionFlag): void
  {
    if($_SESSION[ 'permission' ] !== $permissionFlag)
    {
      die();
    }
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