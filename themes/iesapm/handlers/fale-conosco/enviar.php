<?php

/*--------------------------------------------------------------
FALE CONOSCO

Recebe o POST do formulário (templates/fale-conosco.php), valida o
Cloudflare Turnstile e envia, por SMTP, a confirmação (util/email_template.php)
ao visitante, com cópia oculta (CCO) para EMAIL_DESTINO. Não grava em banco.

Responde com redirect de volta à página do formulário, com
?contato=ok|campos|captcha|erro (o formulário é um POST comum, sem JS).

Variáveis do .env:
  EMAIL_HOST, EMAIL_USER, EMAIL_PASS, EMAIL_PORT, EMAIL_SMTP,
  EMAIL_FROM (opcional), EMAIL_DESTINO, TURNSTILE_SECRET_KEY
--------------------------------------------------------------*/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../inc/env.php';
require_once __DIR__ . '/../util/turnstile.php';
require_once __DIR__ . '/../util/email_template.php';

// O PHPMailer está em lib/phpmailer, mas não é registrado pelo autoload do Composer
require_once __DIR__ . '/../../lib/phpmailer/src/Exception.php';
require_once __DIR__ . '/../../lib/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/../../lib/phpmailer/src/SMTP.php';

function fale_conosco_voltar($status)
{
    $destino = '/';
    $partes  = parse_url($_SERVER['HTTP_REFERER'] ?? '');

    // Só volta para o próprio site (evita open redirect)
    $hostAtual = preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? '');
    if (!empty($partes['host']) && $partes['host'] === $hostAtual) {
        $destino = $partes['path'] ?? '/';
    }

    header('Location: ' . $destino . '?contato=' . $status . '#formContato', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    fale_conosco_voltar('erro');
}

$campo = function ($nome, $max) {
    return mb_substr(trim((string) ($_POST[$nome] ?? '')), 0, $max);
};

$nome       = $campo('nome', 200);
$email      = $campo('email', 200);
$telefone   = $campo('telefone', 50);
$modalidade = $campo('modalidade', 20);
$curso      = $campo('curso', 200);
$mensagem   = $campo('mensagem', 5000);

$modalidades = array('1' => 'Presencial', '2' => 'EAD');
$modalidade  = $modalidades[$modalidade] ?? 'Não informada';

if ($nome === '' || $telefone === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    fale_conosco_voltar('campos');
}

if (!verificarTurnstile($_POST['cf-turnstile-response'] ?? '')) {
    fale_conosco_voltar('captcha');
}

$destinos = array_filter(array_map('trim', explode(',', $_ENV['EMAIL_DESTINO'] ?? '')), function ($e) {
    return filter_var($e, FILTER_VALIDATE_EMAIL);
});

if (empty($_ENV['EMAIL_HOST']) || empty($_ENV['EMAIL_USER']) || empty($destinos)) {
    error_log('[fale-conosco] EMAIL_HOST, EMAIL_USER ou EMAIL_DESTINO ausentes no .env');
    fale_conosco_voltar('erro');
}

$campos = array(
    array('label' => 'Nome', 'valor' => $nome),
    array('label' => 'E-mail', 'valor' => $email),
    array('label' => 'Telefone', 'valor' => $telefone),
    array('label' => 'Modalidade', 'valor' => $modalidade),
);
if ($curso !== '') {
    $campos[] = array('label' => 'Curso', 'valor' => $curso);
}
if ($mensagem !== '') {
    $campos[] = array('label' => 'Mensagem', 'valor' => $mensagem);
}

$corpo = gerarEmailHTML(
    'Contato recebido',
    $nome,
    'O <strong>IESAPM</strong> confirma o recebimento de suas informações enviadas pelo formulário de contato do nosso site. Pedimos, por gentileza, que aguarde nosso retorno.',
    $campos
);

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['EMAIL_HOST'];
    $mail->Port       = (int) ($_ENV['EMAIL_PORT'] ?? 587);
    $mail->SMTPAuth   = !empty($_ENV['EMAIL_PASS']);
    $mail->Username   = $_ENV['EMAIL_USER'];
    $mail->Password   = $_ENV['EMAIL_PASS'] ?? '';
    $mail->SMTPSecure = $_ENV['EMAIL_SMTP'] ?? 'tls';
    if (($_ENV['EMAIL_SMTP'] ?? '') === '') {
        $mail->SMTPAutoTLS = false;
    }
    $mail->CharSet = 'UTF-8';

    // EMAIL_FROM: remetente validado no provedor (ex.: Brevo, onde o login SMTP não é um e-mail de envio)
    $mail->setFrom(!empty($_ENV['EMAIL_FROM']) ? $_ENV['EMAIL_FROM'] : $_ENV['EMAIL_USER'], 'IESAPM');

    // Confirmação para o visitante; a cópia interna vai oculta (CCO) para não expor os endereços
    $mail->addAddress($email, $nome);
    foreach ($destinos as $destino) {
        $mail->addBCC($destino);
    }

    $mail->isHTML(true);
    $mail->Subject = 'Informações recebidas - Fale conosco IESAPM';
    $mail->Body    = $corpo;
    $mail->AltBody = 'O IESAPM recebeu suas informações pelo formulário de contato. Aguarde nosso retorno.';

    $mail->send();
} catch (Exception $e) {
    error_log('[fale-conosco] Falha no envio: ' . $mail->ErrorInfo);
    fale_conosco_voltar('erro');
}

fale_conosco_voltar('ok');
