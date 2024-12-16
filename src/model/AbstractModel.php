<?php

namespace model;

use Throwable;

class AbstractModel
{
  protected ?int $id = null;

  protected static function read(string $where = '', array $whereParameter = [], string $selector = '*', int $limit = -1): array
  {
    global $db;

    $className = self::getClassName();
    try {

      $sql = "SELECT {$selector} FROM {$className} {$className[0]}";

      if($where !== '') {
        $sql .= " WHERE {$where}";
      }

      if($limit !== -1) {
        $sql .= " LIMIT {$limit}";
      }

      $stmt = $db->prepare($sql);
      $stmt->execute($whereParameter);

      if($limit === 1)
      {
        return $stmt->fetch();
      }

      return $stmt->fetchAll();
    } catch (Throwable $exception) {
      die("Error Fetching data from {$className}");
    }
  }

  protected static function insert(array $parameterForInsert): int
  {
    global $db;
    $className = self::getClassName();

    $sql = "INSERT INTO {$className} () VALUES ()";



    return $db->lastInsertId();
  }

  private static function getClassName(): string
  {
    return strtolower(str_replace('model\\', '', get_called_class()));
  }

  public function getId (): int
  {
    return $this->id;
  }

  public function setId (int $id): void
  {
    $this->id = $id;
  }
}