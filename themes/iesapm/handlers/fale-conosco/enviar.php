<?php

/*--------------------------------------------------------------
FALE CONOSCO

Recebe o POST do formulário (templates/fale-conosco.php), valida o
Cloudflare Turnstile e envia a mensagem por SMTP para EMAIL_DESTINO. O e-mail do
visitante entra apenas no Reply-To. Não grava em banco.

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

$linha = function ($rotulo, $valor) {
    return '<p><strong>' . $rotulo . ':</strong> ' . nl2br(htmlspecialchars($valor, ENT_QUOTES, 'UTF-8')) . '</p>';
};

$corpo = $linha('Nome', $nome)
    . $linha('E-mail', $email)
    . $linha('Telefone', $telefone)
    . $linha('Modalidade', $modalidade)
    . $linha('Curso', $curso)
    . $linha('Mensagem', $mensagem);

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
    $mail->setFrom(!empty($_ENV['EMAIL_FROM']) ? $_ENV['EMAIL_FROM'] : $_ENV['EMAIL_USER'], 'Site IESAPM');
    foreach ($destinos as $destino) {
        $mail->addAddress($destino);
    }
    $mail->addReplyTo($email, $nome);

    $mail->isHTML(true);
    $mail->Subject = 'Fale conosco - ' . $nome;
    $mail->Body    = $corpo;
    $mail->AltBody = strip_tags(str_replace(array('<br />', '</p>'), "\n", $corpo));

    $mail->send();
} catch (Exception $e) {
    error_log('[fale-conosco] Falha no envio: ' . $mail->ErrorInfo);
    fale_conosco_voltar('erro');
}

fale_conosco_voltar('ok');
