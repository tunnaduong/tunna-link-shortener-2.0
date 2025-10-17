<?php

namespace App\Database;

use App\Config\DatabaseConfig;
use PDO;
use PDOException;

class DatabaseConnection
{
  private static ?DatabaseConnection $instance = null;
  private ?PDO $connection = null;
  private DatabaseConfig $config;

  private function __construct(DatabaseConfig $config)
  {
    $this->config = $config;
  }

  public static function getInstance(DatabaseConfig $config): DatabaseConnection
  {
    if (self::$instance === null) {
      self::$instance = new self($config);
    }
    return self::$instance;
  }

  public function getConnection(): PDO
  {
    if ($this->connection === null) {
      try {
        $this->connection = new PDO(
          $this->config->getDsn(),
          $this->config->getUsername(),
          $this->config->getPassword(),
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
          ]
        );
      } catch (PDOException $e) {
        throw new \Exception("Database connection failed: " . $e->getMessage());
      }
    }
    return $this->connection;
  }

  public function closeConnection(): void
  {
    $this->connection = null;
  }
}
