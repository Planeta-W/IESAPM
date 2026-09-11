<?php
/**
 * archive
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
                    <h1 class="text-white"><?php post_type_archive_title(); ?></h1>
                </div>
            </div>
        </div>
    </section>

    <section class="pad-page">
        <div class="container">

            <div class="mb-5">
                <?php get_template_part('template-parts/breadcrumbs'); ?>
            </div>

            <div class="row g-4">

                <?php
                if (have_posts()): while (have_posts()) : the_post();
                ?>

                <article id="article-id-<?php the_id();?>" <?php post_class('col-sm-6 col-lg-4 col-xl-3'); ?>>
                    <?php get_template_part('template-parts/card-curso'); ?>
                </article>

                <?php
                endwhile; else: ?>

                <?php
                $post_type_obj = get_post_type_object(get_post_type() ?: get_query_var('post_type'));
                $singular_name = $post_type_obj ? $post_type_obj->labels->singular_name : __('item', 'iesapm');
                ?>

                <div class="col-12 text-center py-5">
                    <p class="fs-5 mb-4"><?php printf(esc_html__('Nenhum %s cadastrado.', 'iesapm'), esc_html(strtolower($singular_name))); ?></p>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">Voltar para home</a>
                </div>

                <?php
                endif;
                wp_reset_query();
                ?>

            </div>

        </div>
    </section>

</main>

<?php get_footer(); ?>
