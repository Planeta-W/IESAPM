<?php
/**
 * single de graduação / pós-graduação / extensão
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

                        <div class="mt-5">
                            <?php // compartilhe nas redes sociais
                            get_template_part('template-parts/compartilhe-social'); ?>
                        </div>
                    </div>

                    <div class="col-xl-3 offset-xl-1">
                        <div class="shadow-lg">

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

                            <div class="d-flex justify-content-center py-3">
                                <?php
                                // verifica se o link é uma página externa
                                $pega_target = get_field('a_inscricao_e_uma_pagina_externa');

                                if($pega_target == 'sim') :
                                $target = '_blank'; ?>

                                <a href="<?php the_field('url_para_inscricao'); ?>" class="btn btn-primary" title="Inscreva-se" target="<?php echo $target; ?>">Inscreva-se</a>

                                <?php else :
                                $target = '_self';
                                ?>

                                <a href="<?php echo get_permalink('44'); ?>" class="btn btn-primary" title="Inscreva-se" target="<?php echo $target; ?>">Inscreva-se</a>

                                <?php
                                endif;
                                ?>

                            </div>
                        </div>
                    </div>

                </div>
            </article>
            <?php endwhile; endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>
