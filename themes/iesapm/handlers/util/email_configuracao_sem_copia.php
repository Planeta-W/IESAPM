<?php

require_once __DIR__ . '/../../inc/env.php';

// NECESSÁRIO ESTAR ANTES DE TUDO
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// PHPMAILER INSTALADO PELO COMPOSER (composer.json NA RAIZ DO PROJETO)
require_once __DIR__ . '/../../lib/autoload.php';

// CREDENCIAIS VÊM DO .env (mesmo arquivo usado pelo wp-config.php)
if (empty($_ENV['EMAIL_USER']) || empty($_ENV['EMAIL_HOST']) || empty($_ENV['EMAIL_PASS'])) {
    http_response_code(500);
    die(json_encode(['status' => 'erro', 'erro' => 'Configuração de e-mail ausente']));
}

$mail = new PHPMailer(true);

$mail->SMTPDebug = 0; // ATIVA O DEBUGGIN PARA VERIFICAÇÃO DE ERROS (remover quando finalizados os testes)
$mail->IsSMTP(); // DEFINE O PADRÃO SMTP
$mail->Host = $_ENV['EMAIL_HOST']; // HOST DO SMTP
$mail->SMTPAuth = true;
$mail->Username = $_ENV['EMAIL_USER']; // EMAIL
$mail->Password = $_ENV['EMAIL_PASS']; // SENHA
$mail->SMTPSecure = $_ENV['EMAIL_SMTP'] ?? 'tls'; // SE O SMTP TIVER CRIPTOGRAFIA, USAR ESTA LINHA
$mail->Port = (int) ($_ENV['EMAIL_PORT'] ?? 587); // PORTA
$mail->From = $_ENV['EMAIL_USER']; // MESMO EMAIL DO USERNAME
$mail->FromName = 'Instituto Oftalmológico Brooklin'; // NOME DO REMETENTE
$mail->CharSet = 'UTF-8'; // CODIFICAÇÃO DO CONTEÚDO

$mail->addAddress($email); // EMAIL DO DESTINATÁRIO (SEM CÓPIA)

$mail->IsHTML(true); // DEFINE QUE O E-MAIL SERÁ ENVIADO COMO HTML
$mail->setLanguage('pt');
$mail->Subject = $subject;
$mail->MsgHTML($corpoMSG);


try {
    $mail->send();
} catch (Exception $e) {
    $responseError = array(
      'status' => 'erro',
      'erro' => "Mailer Error: " . $mail->ErrorInfo
    );
    echo json_encode($responseError);
    exit;
}

?>
