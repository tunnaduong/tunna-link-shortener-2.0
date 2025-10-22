<?php

namespace App\Config;

class DatabaseConfig
{
  private $host;
  private $username;
  private $password;
  private $database;
  private $charset;

  public function __construct()
  {
    // Load configuration from file
    $config = require __DIR__ . '/../../config/database.php';

    $this->host = $config['host'];
    $this->username = $config['username'];
    $this->password = $config['password'];
    $this->database = $config['database'];
    $this->charset = $config['charset'];
  }

  public function getHost(): string
  {
    return $this->host;
  }

  public function getUsername(): string
  {
    return $this->username;
  }

  public function getPassword(): string
  {
    return $this->password;
  }

  public function getDatabase(): string
  {
    return $this->database;
  }

  public function getCharset(): string
  {
    return $this->charset;
  }

  public function getDsn(): string
  {
    return "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
  }
}
