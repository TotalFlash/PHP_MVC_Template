<?php

namespace model;

use Throwable;

class AbstractModel
{
  protected int $id;

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

  private static function getClassName(): string
  {
    return strtolower(str_replace('model\\', '', get_called_class()));
  }
}