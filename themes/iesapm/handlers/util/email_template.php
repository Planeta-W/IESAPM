<?php

/**
 * Monta o HTML do e-mail de confirmação de contato.
 *
 * @param string $titulo    Título exibido no cabeçalho do card (ex: "Contato recebido")
 * @param string $nome      Nome da pessoa que preencheu o formulário
 * @param string $intro     Texto de introdução/confirmação
 * @param array  $campos    Lista de campos: [ ['label' => 'Nome', 'valor' => '...'], ... ]
 *                          Use 'html' => true no item para não escapar o valor (ex: link de anexo)
 * @param array  $opcoes    Opcional (os padrões mantêm o e-mail de confirmação ao visitante):
 *                          'saudacao'     => texto do título (padrão: "Olá, {nome}!")
 *                          'titulo_dados' => cabeçalho da tabela (padrão: "Dados enviados")
 *                          'assinatura'   => false para ocultar "Atenciosamente, IESAPM"
 *                          'rodape'       => texto do rodapé (padrão: "...não é necessário respondê-lo.")
 *                          'botao'        => ['texto' => '...', 'url' => '...'] botão de chamada após a introdução
 * @return string
 */
function gerarEmailHTML($titulo, $nome, $intro, $campos, $opcoes = array())
{
    $saudacao   = isset($opcoes['saudacao']) ? $opcoes['saudacao'] : 'Olá, ' . $nome . '!';
    $tituloDados = isset($opcoes['titulo_dados']) ? $opcoes['titulo_dados'] : 'Dados enviados';
    $rodape     = isset($opcoes['rodape']) ? $opcoes['rodape'] : 'Este é um e-mail automático, não é necessário respondê-lo.';
    $assinatura = !isset($opcoes['assinatura']) || $opcoes['assinatura'];
    $botaoHtml  = '';
    if (!empty($opcoes['botao']['url'])) {
        $botaoHtml = '<p style="margin:20px 0 0 0;"><a href="' . htmlspecialchars($opcoes['botao']['url']) . '" target="_blank" style="display:inline-block; background-color:#00a194; color:#ffffff; font-family:Arial, Helvetica, sans-serif; font-size:15px; font-weight:bold; text-decoration:none; padding:12px 24px; border-radius:6px;">' . htmlspecialchars($opcoes['botao']['texto']) . '</a></p>';
    }

    $corFundo      = '#f2f2f2';
    $corTexto      = '#585858';
    $corTextoSuave = '#7a7a7a';
    $corAccent     = '#00a194';
    $corBorda      = '#ececec';
    $logo          = 'https://www.iesapm.org.br/site/wp-content/uploads/brand.png';

    $linhasCampos = '';
    foreach ($campos as $campo) {
        $valor = isset($campo['html']) && $campo['html'] ? $campo['valor'] : nl2br(htmlspecialchars($campo['valor']));
        $linhasCampos .= '
            <tr>
              <td style="padding:14px 20px; border-bottom:1px solid ' . $corBorda . '; font-family:Arial, Helvetica, sans-serif; font-size:13px; color:' . $corTextoSuave . '; text-transform:uppercase; letter-spacing:.4px; width:150px; vertical-align:top;">
                ' . htmlspecialchars($campo['label']) . '
              </td>
              <td style="padding:14px 20px; border-bottom:1px solid ' . $corBorda . '; font-family:Arial, Helvetica, sans-serif; font-size:15px; color:' . $corTexto . '; vertical-align:top;">
                ' . $valor . '
              </td>
            </tr>';
    }

    return '
    <body style="margin:0; padding:0; background-color:' . $corFundo . ';">
      <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color:' . $corFundo . '; padding:32px 16px;">
        <tr>
          <td align="center">
            <table role="presentation" width="600" border="0" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 2px 10px rgba(0,0,0,0.06);">

              <!-- Cabeçalho -->
              <tr>
                <td style="background-color:#ffffff; padding:32px 40px 24px 40px; text-align:center; border-bottom:3px solid ' . $corAccent . ';">
                  <img src="' . $logo . '" width="220" alt="IESAPM" style="display:block; margin:0 auto;">
                </td>
              </tr>

              <!-- Título -->
              <tr>
                <td style="padding:32px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif;">
                  <p style="margin:0 0 4px 0; font-size:12px; font-weight:bold; letter-spacing:1px; text-transform:uppercase; color:' . $corAccent . ';">' . htmlspecialchars($titulo) . '</p>
                  <h1 style="margin:0; font-size:22px; line-height:1.3; color:' . $corTexto . ';">' . htmlspecialchars($saudacao) . '</h1>
                </td>
              </tr>

              <!-- Introdução -->
              <tr>
                <td style="padding:8px 40px 24px 40px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:1.6; color:' . $corTexto . ';">
                  ' . $intro . $botaoHtml . '
                </td>
              </tr>

              <!-- Tabela de dados -->
              <tr>
                <td style="padding:0 40px 8px 40px;">
                  <table role="presentation" width="100%" border="0" cellpadding="0" cellspacing="0" style="border:1px solid ' . $corBorda . '; border-radius:6px; overflow:hidden;">
                    <tr>
                      <td colspan="2" style="padding:14px 20px; background-color:' . $corFundo . '; font-family:Arial, Helvetica, sans-serif; font-size:13px; font-weight:bold; letter-spacing:.4px; text-transform:uppercase; color:' . $corTexto . ';">
                        ' . htmlspecialchars($tituloDados) . '
                      </td>
                    </tr>
                    ' . $linhasCampos . '
                  </table>
                </td>
              </tr>

              <!-- Assinatura -->
              ' . ($assinatura ? '<tr>
                <td style="padding:28px 40px 8px 40px; font-family:Arial, Helvetica, sans-serif; font-size:15px; line-height:1.6; color:' . $corTexto . ';">
                  Atenciosamente,<br>
                  <strong>IESAPM</strong>
                </td>
              </tr>' : '') . '

              <!-- Rodapé -->
              <tr>
                <td style="padding:24px 40px 32px 40px; border-top:1px solid ' . $corBorda . '; text-align:center;">
                  <a href="https://www.iesapm.org.br/" target="_blank" style="font-family:Arial, Helvetica, sans-serif; font-size:13px; color:' . $corAccent . '; text-decoration:none; font-weight:bold;">iesapm.org.br</a>
                  <p style="margin:12px 0 0 0; font-family:Arial, Helvetica, sans-serif; font-size:11px; color:' . $corTextoSuave . ';">
                    ' . htmlspecialchars($rodape) . '
                  </p>
                </td>
              </tr>

            </table>
          </td>
        </tr>
      </table>
    </body>
    ';
}
