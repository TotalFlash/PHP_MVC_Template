<?php

namespace model;

use Throwable;

class AbstractModel
{
  protected int $id;

  /**
   * Get data from database. When the $where parameter is empty, then all data will be loaded.
   *
   * @param string $where
   * @param array $whereParameter
   * @param string $selector
   * @param int $limit
   *
   * @return array
   */
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

  /**
   * Gets the class name from the child class wich called this method
   *
   * @return string
   */
  private static function getClassName(): string
  {
    return strtolower(str_replace('model\\', '', get_called_class()));
  }
}