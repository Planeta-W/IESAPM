<?php
/**
 * archive corpo docente
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

            <?php if (isset($_GET['homolog'])) : ?>

            <?php
            $areas_order = array(42, 43, 44, 45);

            $areas = get_terms(array(
                'taxonomy'   => 'area',
                'hide_empty' => true,
                'include'    => $areas_order,
            ));

            if (!empty($areas) && !is_wp_error($areas)) {
                usort($areas, function ($a, $b) use ($areas_order) {
                    return array_search($a->term_id, $areas_order) - array_search($b->term_id, $areas_order);
                });
            }
            ?>

            <?php if (!empty($areas) && !is_wp_error($areas)) : ?>

                <?php foreach ($areas as $area) : ?>

                <?php
                $docentes_query = new WP_Query(array(
                    'post_type'      => 'corpo-docente',
                    'posts_per_page' => -1,
                    'tax_query'      => array(
                        array(
                            'taxonomy' => 'area',
                            'field'    => 'term_id',
                            'terms'    => $area->term_id,
                        ),
                    ),
                ));
                ?>

                <?php if ($docentes_query->have_posts()) : ?>

                <div class="mb-5">

                    <h2 class="mb-4"><?php echo esc_html($area->name); ?></h2>

                    <div class="row gy-5 g-sm-3 g-xl-4">

                        <?php while ($docentes_query->have_posts()) : $docentes_query->the_post(); ?>

                        <article id="article-id-<?php the_id();?>" <?php post_class('col-sm-6 col-md-4 col-xl-3'); ?>>
                            <?php get_template_part('template-parts/card-corpo-docente'); ?>
                        </article>

                        <?php endwhile; ?>

                    </div>

                </div>

                <?php endif; ?>

                <?php wp_reset_postdata(); ?>

                <?php endforeach; ?>

            <?php endif; ?>

            <?php else : ?>

            <div class="row gy-5 g-sm-3 g-xl-4">

                <?php if (have_posts()): while (have_posts()) : the_post(); ?>

                <article id="article-id-<?php the_id();?>" <?php post_class('col-sm-6 col-md-4 col-xl-3'); ?>>
                    <?php get_template_part('template-parts/card-corpo-docente'); ?>
                </article>

                <?php
                endwhile;endif;
                wp_reset_query();
                ?>

            </div>

            <?php endif; ?>

        </div>
    </section>

</main>

<?php get_footer(); ?>
