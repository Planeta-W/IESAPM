<?php
/**
 * single de graduação / pós-graduação / extensão / curso livre
 *
 * @package WordPress
 * @subpackage iesapm
 *
 */

?>

<?php get_header(); ?>

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

    <section class="pad-page">
        <div class="container">

            <div class="mb-5">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
            </div>

            <?php if (have_posts()): while (have_posts()) : the_post(); ?>
            <article id="article-id-<?php the_id();?>" <?php post_class(); ?>>
                <div class="row gy-5 gx-xl-5">

                    <div class="col-xl-8">

                        <div class="entry-content-post">
                            <?php the_content(); ?>
                        </div>

                        <?php
                        $coordenadores_posts = get_field('coordenador');
                        if( $coordenadores_posts ): ?>

                        <div class="mt-5">
                            <h2 class="text-primary h5 mb-4">Coordenação</h2>

                            <div class="row g-3">

                                <?php foreach( $coordenadores_posts as $post ): ?>
                                <?php setup_postdata($post); ?>

                                <article id="article-id-<?php the_ID();?>" <?php post_class('col-sm-6 col-lg-4'); ?>>
                                    <?php get_template_part('template-parts/card-corpo-docente'); ?>
                                </article>
                                
                                <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>

                            </div>

                        </div>

                        <?php endif; ?>

                        <?php
                        $professores_posts = get_field('professor');
                        if( $professores_posts ): ?>

                        <div class="mt-5">
                            <h2 class="text-primary h5 mb-4">Professores</h2>

                            <div class="row g-3">

                                <?php foreach( $professores_posts as $post ): ?>
                                <?php setup_postdata($post); ?>

                                <article id="article-id-<?php the_ID();?>" <?php post_class('col-sm-6 col-lg-4'); ?>>
                                    <?php get_template_part('template-parts/card-corpo-docente'); ?>
                                </article>
                                
                                <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>

                            </div>

                        </div>

                        <?php endif; ?>

                        <div class="mt-5">
                            <?php // compartilhe nas redes sociais
                            get_template_part('template-parts/compartilhe-social'); ?>
                        </div>
                    </div>

                    <div class="col-xl-3 offset-xl-1">
                        <?php $lead_modo = get_field('lead_modo_formulario'); ?>
                        <div class="shadow-lg">

                            <?php // sem turma prevista (lista de espera), o bloco de investimento (título, valores e texto) não é exibido ?>
                            <?php if ($lead_modo !== 'espera') : ?>
                            <h2 class="bg-secondary h5 mb-0 p-3 text-center text-white">Investimento</h2>

                            <?php if(get_field('valor_do_curso')): ?>
                            <div class="bg-primary border-bottom small text-center text-white"><p>de: R$ <span class="fs-4 fw-bold" style="text-decoration: line-through;"><?php the_field('valor_do_curso'); ?></span> mensais</p></div>
                            <?php endif; ?>

                            <?php if(get_field('valor_do_curso_com_desconto')): ?>
                            <div class="bg-primary p-1 small text-center text-white"><p>por: R$ <span class="fs-4 fw-bold"><?php the_field('valor_do_curso_com_desconto'); ?></span> mensais</p></div>
                            <?php endif; ?>

                            <?php if(get_field('valores_em_texto')): ?>
                            <div class="bg-primary p-1 pt-3 text-center text-white">
                                <?php the_field('valores_em_texto'); ?>
                            </div>
                            <?php endif; ?>

                            <?php if(get_field('bloco_de_texto')): ?>
                            <div class="bg-light p-3 small text-primary">
                                <?php the_field('bloco_de_texto'); ?>
                            </div>
                            <?php endif; ?>
                            <?php endif; // fim do bloco de investimento ?>

                            <?php
                            $lead_ativo   = in_array($lead_modo, array('inscricao', 'espera'), true);
                            $lead_material = get_field('lead_material_download');

                            // link de inscrição (externo ou página interna), usado pelo botão do formulário
                            $pega_target   = get_field('a_inscricao_e_uma_pagina_externa');
                            $url_inscricao = $pega_target == 'sim' ? get_field('url_para_inscricao') : '';
                            $target        = $pega_target == 'sim' ? '_blank' : '_self';
                            if (empty($url_inscricao)) {
                                $url_inscricao = get_permalink('44');
                                $target        = '_self';
                            }

                            if ($lead_ativo) {
                                get_template_part('template-parts/form-lead-curso', null, array(
                                    'modo'   => $lead_modo,
                                    'url'    => $url_inscricao,
                                    'target' => $target,
                                ));
                            }
                            ?>

                            <?php if ($lead_ativo && !empty($lead_material['url'])) : ?>
                            <div id="leadMaterial" class="p-3 text-center" hidden>
                                <a href="#" class="btn btn-outline-primary" target="_blank" rel="noopener">Baixar material</a>
                            </div>
                            <?php endif; ?>

                            <?php if (!$lead_ativo) : ?>
                            <div class="d-flex justify-content-center py-3">
                                <a href="<?php echo esc_url($url_inscricao); ?>" class="btn btn-primary" title="Inscreva-se" target="<?php echo $target; ?>">Inscreva-se</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </article>
            <?php endwhile; endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>
