<?php
// ======= CRIPTOGRAFIA =======
// ============================

require_once __DIR__ . '/../../inc/env.php';

// CIFRA
$cipher = "aes-256-cbc";

// CHAVES DE SEGURANÇA (vêm do .env, mesmo arquivo usado pelo wp-config.php)
$encryption_key = $_ENV['ENCRYPTION_KEY'];
$iv = $_ENV['ENCRYPTION_IV'];


// FUNÇÃO CRIPTOGRAFIA

function criptografar($var){

  global $cipher;
  global $encryption_key;
  global $iv;

  return openssl_encrypt($var, $cipher, $encryption_key, 0, $iv);
}

// FUNÇÃO DESCRIPTOGRAFIA

function descriptografar($var){

  global $cipher;
  global $encryption_key;
  global $iv;

  return openssl_decrypt($var, $cipher, $encryption_key, 0, $iv);
}

// FUNÇÃO HASH DETERMINÍSTICO (PARA BUSCAS DE IGUALDADE EM CAMPOS CIFRADOS)

function hashBusca($var){

  global $encryption_key;

  return hash_hmac('sha256', trim(mb_strtolower($var)), $encryption_key);
}

?>
