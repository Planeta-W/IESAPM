<?php
/**
 *
 * Template Name: Fale conosco
 * Template Post Type: page
 *
 * @package WordPress
 * @subpackage iesapm
 *
 */

?>

<?php

get_header(); ?>

<main role="main">

    <section class="bg-primary">
        <div class="container">
            <div class="row">
                <div class="col-auto py-3">
                    <h1 class="text-white"><?php the_title(); ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="pad-page pb-0">
        <div class="container">

            <div class="mb-5">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
            </div>

            <div class="row">

                <div class="col-xl-4">

                    <article class="mb-5">
                        <h2 class="fs-5 mb-3">Inscrições e Matrículas</h2>
                        <div class="mb-2">
                            <span class="icon-phone me-1 text-primary"></span>
                            <a class="text-decoration-none" href="tel:+551131884300" title="Contato: Inscrições e Matrículas">11 3188-4300</a> <br>
                            <i class="fa-brands fa-whatsapp me-1 text-primary"></i>
                            <a class="text-decoration-none" href="https://wa.me/+5511975195075" title="Contato: Inscrições e Matrículas" target="_blank">11 97519-5075</a>
                        </div>
                        <div>
                            <span class="icon-envelope me-1 text-primary"></span>
                            <a class="text-decoration-none" href="mailto:comercial@iesapm.org.br" title="E-mail: Inscrições e Matrículas">comercial@iesapm.org.br</a>
                        </div>
                    </article>

                    <article class="mb-5">
                        <h2 class="fs-5 mb-3">CAA – Central de Atendimento ao Aluno</h2>
                        <span class="icon-phone me-1 text-primary"></span>
                        <a class="text-decoration-none" href="callto:+551131884314" title="Contato: CAA – Central de Atendimento ao Aluno">11 3188-4314</a>
                    </article>

                    <article class="mb-5">
                        <h2 class="fs-5 mb-3">Ouvidoria</h2>
                        <div class="mb-2">
                            <span class="icon-phone me-1 text-primary"></span>
                            <a class="text-decoration-none" href="callto:+551131884300" title="Contato: Ouvidoria">11 3188-4300</a>
                        </div>
                        <div>
                            <span class="icon-envelope me-1 text-primary"></span>
                            <a class="text-decoration-none" href="mailto:ouvidoria@iesapm.org.br" title="Email: Ouvidoria">ouvidoria@iesapm.org.br</a>
                        </div>
                    </article>
                    <article class="mb-5">
                        <h2 class="fs-5 mb-3">Local</h2>
                        <span class="icon-location_on me-1 text-primary"></span>
                        <a class="text-decoration-none" href="https://goo.gl/maps/GrjuUxnFsUV6hEND9" target="_blank" title="Localização do IESAPM">Av. Brigadeiro Luís Antônio, 278 Bela Vista - São Paulo/SP</a>
                        <div class="row g-2 mt-3">
                            <div class="col-auto">
                                <a class="btn btn-primary" href="https://goo.gl/maps/663yojzUEPJFhiEn6" target="_blank" title="Como chegar pelo Google Maps">Google Maps</a>
                            </div>
                            <div class="col-auto">
                                <a class="btn btn-primary" href="https://ul.waze.com/ul?place=ChIJB7SzQaxZzpQRXICT0Qva0zE&ll=-23.55285740%2C-46.63806040&navigate=yes&utm_campaign=default&utm_source=waze_website&utm_medium=lm_share_location" target="_blank" title="Como chegar pelo Waze">Waze</a>
                            </div>
                        </div>
                    </article>
                </div>

                <article class="col-xl-7 offset-xl-1">
                    <h2 class="h5 mb-3">Formulário de contato</h2>
                    <?php
                    require_once get_template_directory() . '/inc/env.php';

                    $contato_avisos = array(
                        'ok'      => array('success', 'Mensagem enviada com sucesso! Aguarde nosso contato.'),
                        'campos'  => array('warning', 'Preencha corretamente nome, e-mail e telefone.'),
                        'captcha' => array('warning', 'Confirme a opção "Não sou um robô" para enviar o formulário.'),
                        'erro'    => array('danger', 'Não foi possível enviar sua mensagem. Tente novamente mais tarde.'),
                    );
                    $contato_status = sanitize_key($_GET['contato'] ?? '');
                    if (isset($contato_avisos[$contato_status])) : ?>
                        <div class="alert alert-<?php echo $contato_avisos[$contato_status][0]; ?>" role="alert"><?php echo esc_html($contato_avisos[$contato_status][1]); ?></div>
                    <?php endif; ?>
                    <form id="formContato" name="formContato" method="post" action="<?php echo esc_url(get_template_directory_uri() . '/handlers/fale-conosco/enviar.php'); ?>">
                        <div class="row g-2">
                            <div class="col-xl-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-primary" id="campo-nome" name="nome" placeholder="nome">
                                    <label for="campo-nome" class="fw-bold small text-primary form-label">Nome *</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control border-primary" id="campo-email" placeholder="e-mail" name="email">
                                    <label for="campo-email" class="fw-bold small text-primary form-label">E-mail *</label>
                                </div>
                            </div>
                            <div class="col-lg-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-primary" id="campo-telefone" name="telefone" placeholder="telefone" inputmode="tel" autocomplete="tel-national" maxlength="15">
                                    <label for="campo-telefone" class="fw-bold small text-primary form-label">Telefone *</label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-12">
                                <div class="form-floating mb-3">
                                    <select class="form-select border-primary" name="modalidade" id="campo-modalidade" aria-label="Modalidade">
                                        <option selected>[Selecione]</option>
                                        <option value="1">Presencial</option>
                                        <option value="2">EAD</option>
                                    </select>
                                    <label for="campo-modalidade" class="fw-bold small text-primary form-label">Modalidade</label>
                                </div>
                            </div>
                            <div class="col-lg-6 col-xl-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control border-primary" id="campo-curso" name="curso" placeholder="Curso">
                                    <label for="campo-curso" class="fw-bold small text-primary form-label">Curso</label>
                                </div>
                            </div>
                            <div class="col-xl-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control border-primary" name="mensagem" placeholder="Deixe sua mensagem" id="campo-mensagem" style="height: 100px"></textarea>
                                    <label for="campo-mensagem" class="fw-bold small text-primary form-label">Deixe sua mensagem</label>
                                </div>
                            </div>
                            <div class="cf-turnstile mb-3" data-sitekey="<?php echo esc_attr($_ENV['TURNSTILE_SITE_KEY'] ?? ''); ?>" data-language="pt-br"></div>
                            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
                            <div class="col-xl-12 d-flex justify-content-center justify-content-md-end">
                                <button type="submit" class="btn btn-primary">Enviar</button>
                            </div>
                        </div>
                    </form>
                    <script>
                        // Máscara de telefone: (11) 3188-4300 ou (11) 91234-5678
                        (function () {
                            var tel = document.getElementById('campo-telefone');
                            tel.addEventListener('input', function () {
                                var d = tel.value.replace(/\D/g, '').slice(0, 11);
                                if (d.length > 10) {
                                    tel.value = d.replace(/^(\d{2})(\d{5})(\d{0,4}).*/, '($1) $2-$3');
                                } else if (d.length > 6) {
                                    tel.value = d.replace(/^(\d{2})(\d{4})(\d{0,4}).*/, '($1) $2-$3');
                                } else if (d.length > 2) {
                                    tel.value = d.replace(/^(\d{2})(\d{0,5}).*/, '($1) $2');
                                } else {
                                    tel.value = d;
                                }
                            });
                        })();
                    </script>
                </article>

            </div>

        </div>

        <div class="mt-5">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.4374980086914!2d-46.6382666!3d-23.5527257!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94ce59ac416800eb%3A0x617719578e02b3d4!2sAv.%20Brigadeiro%20Lu%C3%ADs%20Ant%C3%B4nio%2C%20278%20-%20Bela%20Vista%2C%20S%C3%A3o%20Paulo%20-%20SP%2C%2001318-000!5e0!3m2!1spt-BR!2sbr!4v1680271695125!5m2!1spt-BR!2sbr" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </section>

</main>

<?php get_footer(); ?>
