<?php

require_once __DIR__ . '/../../inc/env.php';

/**
 * Valida o token do reCAPTCHA contra a API do Google (siteverify).
 *
 * @param string $token Valor de $_POST['g-recaptcha-response']
 * @return bool
 */
function verificarRecaptcha($token) {
  $secret = $_ENV['RECAPTCHA_SECRET_KEY'] ?? '';

  if (empty($secret) || empty($token)) {
    return false;
  }

  $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
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
    error_log('reCAPTCHA: falha ao contatar o Google - ' . $curlErro);
    return false;
  }

  $resultado = json_decode($response, true);

  return !empty($resultado['success']);
}

?>
