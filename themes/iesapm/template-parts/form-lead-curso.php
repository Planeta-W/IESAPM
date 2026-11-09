<?php
/**
 * Formulário de captação de lead da página de curso.
 * Espera a variável $args['modo'] = 'inscricao' | 'espera'
 */

$modo = $args['modo'] ?? 'inscricao';
require_once get_template_directory() . '/inc/env.php';

$titulo = $modo === 'espera' ? 'Quero ser avisado das próximas turmas' : 'Preencha para se inscrever';
$botao  = $modo === 'espera' ? 'Avise-me' : 'Inscreva-se';
$url    = $args['url'] ?? '';
$target = $args['target'] ?? '_self';
$tem_material = !empty($args['material']);
?>

<form id="formLeadCurso" class="p-3 bg-light" data-modo="<?php echo esc_attr($modo); ?>" data-url="<?php echo esc_url($url); ?>" data-target="<?php echo esc_attr($target); ?>" novalidate>
    <h3 class="h6 text-primary mb-3"><?php echo esc_html($titulo); ?></h3>

    <?php if ($tem_material) : ?>
    <div id="leadMaterialAviso" class="bg-white border border-secondary rounded small p-2 mb-3 d-flex align-items-start">
        <i class="fa-solid fa-gift text-secondary fs-5 me-2 mt-1" aria-hidden="true"></i>
        <span><strong class="text-primary">Material complementar gratuito!</strong>
            <?php echo $modo === 'espera' ? 'Cadastre-se e receba' : 'Inscreva-se e receba'; ?> no seu e-mail um material exclusivo deste curso.</span>
    </div>
    <?php endif; ?>

    <input type="hidden" name="action" value="lead_curso">
    <input type="hidden" name="curso_id" value="<?php echo esc_attr(get_the_ID()); ?>">

    <div class="form-floating mb-2">
        <input type="text" class="form-control border-primary" id="lead-nome" name="nome" placeholder="Nome" required>
        <label for="lead-nome" class="fw-bold small text-primary form-label">Nome *</label>
    </div>
    <div class="form-floating mb-2">
        <input type="email" class="form-control border-primary" id="lead-email" name="email" placeholder="E-mail" required>
        <label for="lead-email" class="fw-bold small text-primary form-label">E-mail *</label>
    </div>
    <div class="form-floating mb-2">
        <input type="tel" class="form-control border-primary" id="lead-celular" name="celular" placeholder="Celular" inputmode="numeric" maxlength="15" required>
        <label for="lead-celular" class="fw-bold small text-primary form-label">Celular *</label>
    </div>
    <div class="form-floating mb-3">
        <input type="text" class="form-control border-primary" id="lead-profissao" name="profissao" placeholder="Profissão" required>
        <label for="lead-profissao" class="fw-bold small text-primary form-label">Profissão *</label>
    </div>

    <div class="form-check mb-3 small">
        <input class="form-check-input" type="checkbox" id="lead-consentimento" name="consentimento" value="1" required>
        <label class="form-check-label" for="lead-consentimento">
            Li e concordo com a <a href="<?php echo esc_url(home_url('/politica-de-privacidade/')); ?>" target="_blank">Política de Privacidade</a>.
        </label>
    </div>

    <div class="cf-turnstile mb-3" data-sitekey="<?php echo esc_attr($_ENV['TURNSTILE_SITE_KEY'] ?? ''); ?>" data-language="pt-br" data-callback="leadTurnstileOk" data-expired-callback="leadTurnstileExpirou" data-error-callback="leadTurnstileExpirou"></div>

    <div id="leadCursoMensagem" class="small mb-3" role="alert" aria-live="polite" hidden></div>

    <div class="d-grid">
        <button type="submit" class="btn btn-primary<?php echo $modo === 'inscricao' ? ' opacity-50' : ''; ?>"<?php echo $modo === 'inscricao' ? ' aria-disabled="true"' : ''; ?>><?php echo esc_html($botao); ?></button>
    </div>
</form>
