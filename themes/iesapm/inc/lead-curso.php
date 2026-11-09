<?php
/*--------------------------------------------------------------
LEAD NAS PÁGINAS DE CURSO

- Campos ACF (modo do formulário e material para download)
- Handler AJAX que valida o Turnstile e envia o lead por e-mail
  (mesmo SMTP do fale-conosco). A integração com a RD Station
  entra depois, dentro de lead_curso_enviar_rd().
--------------------------------------------------------------*/

/*--------------------------------------------------------------
CAMPOS ACF
--------------------------------------------------------------*/
function lead_curso_registrar_campos() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'    => 'group_lead_curso',
		'title'  => 'Formulário de captação (lead)',
		'fields' => array(
			array(
				'key'           => 'field_lead_curso_modo',
				'label'         => 'Modo do formulário',
				'name'          => 'lead_modo_formulario',
				'type'          => 'select',
				'instructions'  => 'Libera inscrição: o botão "Inscreva-se" só é liberado após o preenchimento. Lista de espera: não há turma aberta, o formulário apenas capta interessados.',
				'choices'       => array(
					'desativado' => 'Desativado',
					'inscricao'  => 'Libera inscrição (formulário antes do botão)',
					'espera'     => 'Lista de espera (sem turma prevista)',
				),
				'default_value' => 'desativado',
				'return_format' => 'value',
				'allow_null'    => 0,
				'ui'            => 0,
			),
			array(
				'key'               => 'field_lead_curso_material',
				'label'             => 'Material para download',
				'name'              => 'lead_material_download',
				'type'              => 'file',
				'instructions'      => 'Opcional (e-book, guia etc.). O link só é exibido após o envio do formulário.',
				'return_format'     => 'array',
				'library'           => 'all',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'field_lead_curso_modo',
							'operator' => '!=',
							'value'    => 'desativado',
						),
					),
				),
			),
		),
		'location' => array(
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'graduacao' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'pos-graduacao' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'extensao' ) ),
			array( array( 'param' => 'post_type', 'operator' => '==', 'value' => 'cursos_livres' ) ),
		),
		'position' => 'normal',
		'style'    => 'default',
	) );
}
add_action( 'acf/init', 'lead_curso_registrar_campos' );


/*--------------------------------------------------------------
SCRIPT DO FORMULÁRIO (só nas páginas de curso com formulário ativo)
--------------------------------------------------------------*/
function lead_curso_enqueue() {

	if ( ! is_singular( array( 'graduacao', 'pos-graduacao', 'extensao', 'cursos_livres' ) ) ) {
		return;
	}

	$modo = function_exists( 'get_field' ) ? get_field( 'lead_modo_formulario' ) : '';
	if ( ! in_array( $modo, array( 'inscricao', 'espera' ), true ) ) {
		return;
	}

	wp_enqueue_script( 'turnstile', 'https://challenges.cloudflare.com/turnstile/v0/api.js', array(), null, array( 'strategy' => 'defer', 'in_footer' => true ) );
	wp_enqueue_script( 'lead-curso', get_template_directory_uri() . '/assets/js/lead-curso.js', array(), filemtime( get_template_directory() . '/assets/js/lead-curso.js' ), true );
	wp_localize_script( 'lead-curso', 'leadCurso', array(
		'ajaxUrl' => admin_url( 'admin-ajax.php' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'lead_curso_enqueue' );


/*--------------------------------------------------------------
INTEGRAÇÃO RD STATION (etapa futura)
--------------------------------------------------------------*/
function lead_curso_enviar_rd( $lead ) {
	// TODO: enviar $lead (nome, email, celular, profissao, curso, modo) à RD Station.
	return true;
}


/*--------------------------------------------------------------
HANDLER AJAX
--------------------------------------------------------------*/
function lead_curso_responder( $sucesso, $mensagem, $extra = array() ) {
	wp_send_json( array_merge( array( 'success' => $sucesso, 'message' => $mensagem ), $extra ) );
}

function lead_curso_handler() {

	require_once get_template_directory() . '/inc/env.php';
	require_once get_template_directory() . '/handlers/util/turnstile.php';
	require_once get_template_directory() . '/handlers/util/email_template.php';

	$campo = function ( $nome, $max ) {
		return mb_substr( trim( wp_unslash( (string) ( $_POST[ $nome ] ?? '' ) ) ), 0, $max );
	};

	$nome      = $campo( 'nome', 200 );
	$email     = $campo( 'email', 200 );
	$celular   = $campo( 'celular', 30 );
	$profissao = $campo( 'profissao', 200 );
	$curso_id  = absint( $_POST['curso_id'] ?? 0 );

	$celular_digitos = preg_replace( '/\D/', '', $celular );

	if ( $nome === '' || $profissao === '' || ! filter_var( $email, FILTER_VALIDATE_EMAIL ) || strlen( $celular_digitos ) < 10 || strlen( $celular_digitos ) > 13 ) {
		lead_curso_responder( false, 'Preencha corretamente nome, e-mail, celular e profissão.' );
	}

	if ( empty( $_POST['consentimento'] ) ) {
		lead_curso_responder( false, 'É necessário aceitar a Política de Privacidade para continuar.' );
	}

	$curso = $curso_id ? get_post( $curso_id ) : null;
	// Rascunho só é aceito para quem pode editar o curso (pré-visualização no admin)
	$curso_visivel = $curso && ( $curso->post_status === 'publish' || current_user_can( 'edit_post', $curso_id ) );
	if ( ! $curso_visivel || ! in_array( $curso->post_type, array( 'graduacao', 'pos-graduacao', 'extensao', 'cursos_livres' ), true ) ) {
		lead_curso_responder( false, 'Curso não encontrado.' );
	}

	$modo = get_field( 'lead_modo_formulario', $curso_id );
	if ( ! in_array( $modo, array( 'inscricao', 'espera' ), true ) ) {
		lead_curso_responder( false, 'Formulário indisponível para este curso.' );
	}

	if ( ! verificarTurnstile( $_POST['cf-turnstile-response'] ?? '' ) ) {
		lead_curso_responder( false, 'Confirme a verificação de segurança e tente novamente.' );
	}

	$destinos = array_filter( array_map( 'trim', explode( ',', $_ENV['EMAIL_DESTINO'] ?? '' ) ), function ( $e ) {
		return filter_var( $e, FILTER_VALIDATE_EMAIL );
	} );

	if ( empty( $_ENV['EMAIL_HOST'] ) || empty( $_ENV['EMAIL_USER'] ) || empty( $destinos ) ) {
		error_log( '[lead-curso] EMAIL_HOST, EMAIL_USER ou EMAIL_DESTINO ausentes no .env' );
		lead_curso_responder( false, 'Não foi possível enviar seus dados. Tente novamente mais tarde.' );
	}

	$titulo_curso = get_the_title( $curso );
	$link_curso   = get_permalink( $curso );
	$material     = get_field( 'lead_material_download', $curso_id );
	$material_url = is_array( $material ) && ! empty( $material['url'] ) ? $material['url'] : '';
	$espera       = $modo === 'espera';

	$lead = array(
		'nome'      => $nome,
		'email'     => $email,
		'celular'   => $celular,
		'profissao' => $profissao,
		'curso'     => $titulo_curso,
		'modo'      => $modo,
	);

	/* ---------- E-mail 1: confirmação para o interessado ---------- */
	$campos_interessado = array(
		array( 'label' => 'Curso', 'valor' => $titulo_curso ),
		array( 'label' => 'Nome', 'valor' => $nome ),
		array( 'label' => 'E-mail', 'valor' => $email ),
		array( 'label' => 'Celular', 'valor' => $celular ),
		array( 'label' => 'Profissão', 'valor' => $profissao ),
	);

	if ( $espera ) {
		$intro_interessado = 'Registramos seu interesse no curso <strong>' . esc_html( $titulo_curso ) . '</strong>. Assim que houver novas turmas, entraremos em contato com você.';
	} else {
		$intro_interessado = 'Recebemos seus dados para o curso <strong>' . esc_html( $titulo_curso ) . '</strong>. Você já pode concluir a sua inscrição pelo site.';
	}
	if ( $material_url ) {
		$intro_interessado .= '<br><br>Como cortesia, aqui está o material complementar do curso: <a href="' . esc_url( $material_url ) . '">baixar material</a>.';
	}

	$opcoes_interessado = array();
	if ( ! $espera ) {
		$opcoes_interessado['botao'] = array( 'texto' => 'Ir para a página do curso', 'url' => $link_curso );
	}

	$corpo_interessado = gerarEmailHTML(
		$espera ? 'Interesse recebido' : 'Inscrição liberada',
		$nome,
		$intro_interessado,
		$campos_interessado,
		$opcoes_interessado
	);

	/* ---------- E-mail 2: aviso de novo lead para a equipe ---------- */
	$campos_equipe = array(
		array( 'label' => 'Tipo', 'valor' => $espera ? 'Lista de espera (novas turmas)' : 'Inscrição' ),
		array( 'label' => 'Curso', 'valor' => $titulo_curso ),
		array( 'label' => 'Nome', 'valor' => $nome ),
		array( 'label' => 'E-mail', 'valor' => $email ),
		array( 'label' => 'Celular', 'valor' => $celular ),
		array( 'label' => 'Profissão', 'valor' => $profissao ),
		array( 'label' => 'Recebido em', 'valor' => wp_date( 'd/m/Y H:i' ) ),
		array(
			'label' => 'Página',
			'valor' => '<a href="' . esc_url( $link_curso ) . '">' . esc_html( $link_curso ) . '</a>',
			'html'  => true,
		),
	);
	if ( $material_url ) {
		$campos_equipe[] = array( 'label' => 'Material', 'valor' => 'Link enviado ao interessado' );
	}

	$corpo_equipe = gerarEmailHTML(
		$espera ? 'Novo lead - lista de espera' : 'Novo lead - inscrição',
		$nome,
		'Um novo interessado preencheu o formulário do curso <strong>' . esc_html( $titulo_curso ) . '</strong> no site. Responda este e-mail para falar diretamente com ele.',
		$campos_equipe,
		array(
			'saudacao'     => 'Novo lead pelo site',
			'titulo_dados' => 'Dados do lead',
			'assinatura'   => false,
			'rodape'       => 'Aviso automático do formulário de cursos do site.',
		)
	);

	// O WordPress já traz o PHPMailer (carregar o de lib/ causaria redeclaração de classe)
	require_once ABSPATH . WPINC . '/PHPMailer/Exception.php';
	require_once ABSPATH . WPINC . '/PHPMailer/PHPMailer.php';
	require_once ABSPATH . WPINC . '/PHPMailer/SMTP.php';

	// Cria e envia um e-mail; $montar recebe o PHPMailer já com SMTP e remetente configurados
	$enviar = function ( $montar ) {
		$mail = new PHPMailer\PHPMailer\PHPMailer( true );
		try {
			$mail->isSMTP();
			$mail->Host       = $_ENV['EMAIL_HOST'];
			$mail->Port       = (int) ( $_ENV['EMAIL_PORT'] ?? 587 );
			$mail->SMTPAuth   = ! empty( $_ENV['EMAIL_PASS'] );
			$mail->Username   = $_ENV['EMAIL_USER'];
			$mail->Password   = $_ENV['EMAIL_PASS'] ?? '';
			$mail->SMTPSecure = $_ENV['EMAIL_SMTP'] ?? 'tls';
			if ( ( $_ENV['EMAIL_SMTP'] ?? '' ) === '' ) {
				$mail->SMTPAutoTLS = false;
			}
			$mail->CharSet = 'UTF-8';
			$mail->setFrom( ! empty( $_ENV['EMAIL_FROM'] ) ? $_ENV['EMAIL_FROM'] : $_ENV['EMAIL_USER'], 'IESAPM' );
			$mail->isHTML( true );

			$montar( $mail );
			$mail->send();
			return true;
		} catch ( PHPMailer\PHPMailer\Exception $e ) {
			error_log( '[lead-curso] Falha no envio: ' . $mail->ErrorInfo );
			return false;
		}
	};

	$ok_interessado = $enviar( function ( $mail ) use ( $email, $nome, $espera, $titulo_curso, $corpo_interessado ) {
		$mail->addAddress( $email, $nome );
		$mail->Subject = ( $espera ? 'Recebemos seu interesse' : 'Sua inscrição' ) . ' - ' . $titulo_curso;
		$mail->Body    = $corpo_interessado;
		$mail->AltBody = 'O IESAPM recebeu suas informações sobre o curso ' . $titulo_curso . '.';
	} );

	$ok_equipe = $enviar( function ( $mail ) use ( $destinos, $email, $nome, $celular, $espera, $titulo_curso, $corpo_equipe ) {
		foreach ( $destinos as $destino ) {
			$mail->addAddress( $destino );
		}
		$mail->addReplyTo( $email, $nome );
		$mail->Subject = '[Lead] ' . ( $espera ? 'Lista de espera' : 'Inscrição' ) . ' - ' . $titulo_curso . ' - ' . $nome;
		$mail->Body    = $corpo_equipe;
		$mail->AltBody = 'Novo lead: ' . $nome . ' (' . $email . ', ' . $celular . ') - ' . $titulo_curso . '.';
	} );

	// Só falha para o visitante se nenhum dos dois e-mails saiu; um deles já basta para não perder o lead
	if ( ! $ok_interessado && ! $ok_equipe ) {
		lead_curso_responder( false, 'Não foi possível enviar seus dados. Tente novamente mais tarde.' );
	}

	lead_curso_enviar_rd( $lead );

	lead_curso_responder( true, $espera
		? 'Cadastro realizado! Avisaremos você quando houver novas turmas.'
		: 'Dados enviados com sucesso! Sua inscrição foi liberada.', array(
		'material' => $material_url,
	) );
}
add_action( 'wp_ajax_lead_curso', 'lead_curso_handler' );
add_action( 'wp_ajax_nopriv_lead_curso', 'lead_curso_handler' );
