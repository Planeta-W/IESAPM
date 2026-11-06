<?php

require_once __DIR__ . '/../../inc/env.php';

/**
 * Valida o token do Cloudflare Turnstile contra a API (siteverify).
 *
 * @param string $token Valor de $_POST['cf-turnstile-response']
 * @return bool
 */
function verificarTurnstile($token) {
  $secret = $_ENV['TURNSTILE_SECRET_KEY'] ?? '';

  if (empty($secret) || empty($token)) {
    return false;
  }

  $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
    'secret'   => $secret,
    'response' => $token,
    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
  )));
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_TIMEOUT, 10);
  $response = curl_exec($ch);
  $curlErro = curl_error($ch);

  if ($response === false) {
    error_log('Turnstile: falha ao contatar a Cloudflare - ' . $curlErro);
    return false;
  }

  $resultado = json_decode($response, true);

  return !empty($resultado['success']);
}
