<?php
/**
 * Front-page
 *
 * @package WordPress
 * @subpackage iesapm
 *
 */

?>

<?php get_header(); ?>

<main role="main">

    <?php get_template_part('template-parts/banner-top'); ?>

    <?php 
    $args = array(
        'post_type' => 'graduacao',
        'posts_per_page' => 8,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $query_graduacao = new WP_Query($args);
    
    if ($query_graduacao->have_posts()):?>
    <section class="pad-featured pb-0">
        <div class="container">
            <h2 class="mb-4 text-center text-uppercase">Graduação</h2>
            <div class="swiper position-relative mt-4 js-carousel-cursos">
                <div class="swiper-wrapper">

                    <?php while ($query_graduacao->have_posts()) : $query_graduacao->the_post(); ?>

                    <article id="article-id-<?php the_ID();?>" <?php post_class('swiper-slide item h-100'); ?>>
                        <?php get_template_part('template-parts/card-curso'); ?>
                    </article>

                    <?php endwhile; ?>

                </div>

                <div class="swiper-pagination"></div>

                <div class="swiper-button-prev"><span class="icon-arrow_back_ios"></span></div>
                <div class="swiper-button-next"><span class="icon-arrow_forward_ios"></span></div>
            </div>

            <div class="d-flex justify-content-center mt-5">
                <a href="<?php echo get_post_type_archive_link('graduacao'); ?>" title="Ver todos os cursos de Graduação" class="btn btn-secondary">Ver todos</a>
            </div>

        </div>
    </section>
    <?php
    wp_reset_postdata();
    endif;
    ?>

    <?php
    $args = array(
        'post_type' => 'pos-graduacao',
        'posts_per_page' => 8,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $query_pos = new WP_Query($args);

    if ($query_pos->have_posts()):?>
    <section class="pad-featured pb-0">
        <div class="container">
            <h2 class="mb-4 text-center text-uppercase">Pós-Graduação</h2>
            <div class="swiper position-relative mt-4 js-carousel-cursos">
                <div class="swiper-wrapper">

                    <?php while ($query_pos->have_posts()) : $query_pos->the_post(); ?>

                    <article id="article-id-<?php the_ID();?>" <?php post_class('swiper-slide item h-100'); ?>>
                        <?php get_template_part('template-parts/card-curso'); ?>
                    </article>

                    <?php endwhile; ?>

                </div>

                <div class="swiper-pagination"></div>

                <div class="swiper-button-prev"><span class="icon-arrow_back_ios"></span></div>
                <div class="swiper-button-next"><span class="icon-arrow_forward_ios"></span></div>
            </div>

            <div class="d-flex justify-content-center mt-5">
                <a href="<?php echo get_post_type_archive_link('pos-graduacao'); ?>" title="Ver todos os cursos de Pós-Graduação" class="btn btn-secondary">Ver todos</a>
            </div>

        </div>
    </section>
    <?php
    wp_reset_postdata();
    endif;
    ?>

    <?php
    $args = array(
        'post_type' => 'extensao',
        'posts_per_page' => 8,
        'orderby' => 'title',
        'order' => 'ASC'
    );
    $query_extensao = new WP_Query($args);

    if ($query_extensao->have_posts()): ?>
    <section class="pad-featured">
        <div class="container">
            <h2 class="mb-4 text-center text-uppercase">Extensão</h2>
            <div class="swiper position-relative mt-4 js-carousel-cursos">
                <div class="swiper-wrapper">

                    <?php while ($query_extensao->have_posts()) : $query_extensao->the_post(); ?>

                    <article id="article-id-<?php the_ID();?>" <?php post_class('swiper-slide item h-100'); ?>>
                        <?php get_template_part('template-parts/card-curso'); ?>
                    </article>

                    <?php endwhile; ?>

                </div>

                <div class="swiper-pagination"></div>

                <div class="swiper-button-prev"><span class="icon-arrow_back_ios"></span></div>
                <div class="swiper-button-next"><span class="icon-arrow_forward_ios"></span></div>
            </div>

            <div class="d-flex justify-content-center mt-5">
                <a href="<?php echo get_post_type_archive_link('extensao'); ?>" title="Ver todos os cursos de Extensão" class="btn btn-secondary">Ver todos</a>
            </div>

        </div>
    </section>
    <?php
    wp_reset_postdata();
    endif;
    ?>

    <section class="pad-featured bg-light">
        <div class="container">
            <h2 class="mb-4 text-center text-uppercase">Notícias</h2>

            <div class="row gy-5">

                <?php
                $args = array(
                    'post_type'      => 'post',
                    'posts_per_page' => 4,
                );
                $query_news = new WP_Query($args);

                if ($query_news->have_posts()):
                while ($query_news->have_posts()) : $query_news->the_post(); ?>

                <article id="article-id-<?php the_ID();?>" <?php post_class('col-sm-6 col-xl-3 h-100'); ?>>
                    <?php // card post
                    get_template_part('template-parts/card-post'); ?>
                </article>

                <?php endwhile; wp_reset_postdata();
                endif; ?>

            </div>

            <div class="d-flex justify-content-center mt-5">
                <a href="<?php echo get_permalink('2701'); ?>" title="Ver todas as notícias" class="btn btn-secondary">Ver todos</a>
            </div>

        </div>
    </section>

    <section class="pad-featured" style="background-image: url('<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/uploads/bg-news.jpg'); background-size: cover;">
        <div class="container">
            <h2 class="mb-4 text-center text-uppercase text-white">Conecte-se a IESAPM</h2>
            <form id="formNews" name="formNews" method="post" action="<?php bloginfo('url')?>/form_action/news.php" class="row">
                <div class="col-lg-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0" id="campo-nome" name="nome" placeholder="Nome completo">
                        <label for="campo-nome" class="fw-bold small text-primary">Nome completo</label>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control border-0" id="campo-email" name="email" placeholder="E-mail">
                        <label for="campo-email" class="fw-bold small text-primary">E-mail</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control border-0" id="campo-telefone" name="celular" placeholder="Telefone">
                        <label for="campo-telefone" class="fw-bold small text-primary">Telefone</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select class="form-select border-0" id="select-curso-interesse" name="tipo_curso" aria-label="Selecione o curso de interesse">
                            <option selected>[Selecione]</option>
                            <option value="1">Graduação</option>
                            <option value="2">Pós-Graduação</option>
                            <option value="3">Extensão</option>
                        </select>
                        <label for="select-curso-interesse" class="fw-bold small text-primary">Curso de interesse</label>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="form-floating mb-3">
                        <select class="form-select border-0" id="select-campo-area" name="curso" aria-label="Selecione a sua área de interesse">
                            <option selected>[Selecione]</option>
                            <option value="1">Médica</option>
                            <option value="2">Multi-profissional</option>
                        </select>
                        <label for="select-campo-area" class="fw-bold small text-primary">Área de interesse</label>
                    </div>
                </div>
                <div class="col-12 d-flex justify-content-center">
                    <input type="submit" value="Cadastrar" class="btn btn-secondary">
                </div>
            </form>
        </div>
    </section>

</main>

<?php get_footer(); ?>

<script>
jQuery(document).ready(function ($) {

    // carousel dos cursos
    document.querySelectorAll('.js-carousel-cursos').forEach(function (el) {
        new Swiper(el, {
            slidesPerView: 1,
            spaceBetween: 24,
            loop: false,
            autoplay: false,

            pagination: {
                el: el.querySelector('.swiper-pagination'),
                clickable: true,
            },

            navigation: {
                nextEl: el.querySelector('.swiper-button-next'),
                prevEl: el.querySelector('.swiper-button-prev'),
            },

            breakpoints: {
                0: {
                    slidesPerView: 1,
                },

                576: {
                    slidesPerView: 2,
                },

                768: {
                    slidesPerView: 2,
                },

                992: {
                    slidesPerView: 3,
                },

                1200: {
                    slidesPerView: 4,
                },
            }
        });
    });

    var body = $('body');
});
</script>
