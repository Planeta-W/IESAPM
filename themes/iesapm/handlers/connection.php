<?php
// Dados de conexão

// ===== FUNÇÃO CONECTAR ======
// ============================
require_once __DIR__ . '/../inc/env.php';

function conectar(){
  $server = $_ENV['DB_HOST'];
  $user   = $_ENV['DB_USER'];
  $pass   = $_ENV['DB_PASS'];
  $db     = $_ENV['DB_NAME'];

  try {
      $pdo = new PDO("mysql:host=$server;dbname=$db", $user, $pass );
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $pdo->exec("SET CHARACTER SET utf8");

  } catch (\Throwable $th) {
      return $th;
  }

  return $pdo;
}

?>