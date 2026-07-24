<?php
/**
 * single padrão
 *
 * @package WordPress
 * @subpackage iesapm
 *
 */

?>

<?php get_header(); ?>

<?php
$id_post = get_the_id(); ?>

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

            <div class="mb-3 mb-xl-5">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
            </div>

            <div class="row gy-5 gx-xl-5">

                <div class="col-xl-8">

                    <?php if (have_posts()): while (have_posts()) : the_post(); ?>
                    <article id="article-id-<?php the_id();?>" <?php post_class(); ?>>

                        <div class="bg-light border-bottom gx-1 mb-5 p-2 row">
                            <div class="col-auto c-post-metadata">
                                <span class="icon-calendar me-1 small text-dark"></span>
                                <span class="small"><?php the_time('d/m/Y'); ?></span>
                            </div>
                            <div class="col-auto c-post-metadata ms-3">
                                <span class="icon-price-tag me-1 small text-dark"></span>
                                <?php the_category(', '); ?>
                            </div>
                            <div class="col-auto c-post-metadata d-none">
                                <span class="icon-blog-1"></span>
                                <span class="small"><?php the_author(); ?></span>
                            </div>
                        </div>

                        <div class="entry-content-post">
                            <?php if (has_post_thumbnail() && get_field('exibir_imagem_destacada_em_post') == 1) : ?>
                            <div class="mb-4">
                                <?php the_post_thumbnail('full', array('class' => 'img-fluid w-100')); ?>
                            </div>
                            <?php endif; ?>

                            <?php the_content(); ?>

                            <div>
                                <?php // compartilhe nas redes sociais
                                get_template_part('template-parts/compartilhe-social'); ?>
                            </div>

                        </div>

                    </article>
                    <?php endwhile; endif; ?>

                </div>

                <aside class="col-xl-4">

                    <div class="mt-5 mt-xl-0 mb-5">
                        <?php get_template_part('template-parts/search'); ?>
                    </div>

                    <div>
                        <h2 class="mb-4 text-primary h5">Veja também</h2>

                        <div class="row gy-5">

                            <?php
                            $args = array(
                                'post_type'      => 'post',
                                'posts_per_page' => 2,
                                'post__not_in'   => array($id_post),
                            );
                            $query_related = new WP_Query($args);

                            if ($query_related->have_posts()):
                            while ($query_related->have_posts()) : $query_related->the_post(); ?>

                            <article id="article-id-<?php the_ID();?>" <?php post_class('col-sm-6 col-xl-12 h-100'); ?>>
                                <?php // card post
                                get_template_part('template-parts/card-post'); ?>
                            </article>

                            <?php endwhile; wp_reset_postdata();
                            endif; ?>

                        </div>

                    </div>

                </aside>
            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>
