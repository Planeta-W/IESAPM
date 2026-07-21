<div class="l-banner-header">
    <div>
        <div id="js-carousel-banner-header" class="swiper position-relative">
            <div class="swiper-wrapper">

                <?php
                $args_slide = array(
                    'post_type'      => 'banner-header',
                    'posts_per_page' => -1,
                    'post_status'    => 'publish',
                    'orderby'        => 'menu_order',
                    'order'          => 'DESC',
                );
                query_posts($args_slide);

                if (have_posts()): while (have_posts()) : the_post();

                // variaveis
                $banner_mobile = get_field('banner_mobile');
                $banner_desktop = get_field('banner_desktop');
                $link_banner = get_field('link_do_banner');
                ?>

                <div class="swiper-slide item">
                    <a href="<?php echo $link_banner; ?>" title="<?php the_title(); ?>">
                        <picture>
                            <source srcset="<?php echo $banner_desktop['url']; ?>" media="(min-width: 768px)" >
                            <img src="<?php echo $banner_mobile['url']; ?>" class="img-fluid w-100" alt="<?php the_title(); ?>">
                        </picture>
                    </a>
                </div>

                <?php
                endwhile;endif;
                wp_reset_query();
                ?>

            </div>

            <div class="swiper-pagination"></div>

            <div class="swiper-button-prev"><span class="icon-arrow_back_ios"></span></div>
            <div class="swiper-button-next"><span class="icon-arrow_forward_ios"></span></div>
         </div>
    </div>
</div>

<script>
jQuery(document).ready(function ($) {

    // carousel do banner do topo
    new Swiper('#js-carousel-banner-header', {
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        autoHeight: true,

        pagination: {
            el: '#js-carousel-banner-header .swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '#js-carousel-banner-header .swiper-button-next',
            prevEl: '#js-carousel-banner-header .swiper-button-prev',
        },
    });

    var body = $('body');
});
</script>
