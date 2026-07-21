<div id="js-banner-swiper" class="c-banner-swiper bg-primary position-relative overflow-hidden">

    <div class="swiper">
        <div class="swiper-wrapper">

            <?php
            $args = array(
                'post_type'      => 'banner-header',
                'posts_per_page' => -1,
                'orderby'        => 'date',
                'order'          => 'ASC',
                'post_status'    => 'publish',

                'meta_query' => array(
                    'relation' => 'AND', // Ambas as condições devem ser verdadeiras

                    // Condição para a data de início
                    array(
                        'key'     => 'data_de_inicio',
                        'value'   => date("Y-m-d"), // Define a data de hoje
                        'compare' => '<=', // Exibe posts com data de início menor ou igual a hoje
                        'type'    => 'DATE' // Informa que estamos lidando com data
                    ),

                    // Condição para a data de expiração, caso o campo esteja ativo
                    array(
                        'relation' => 'OR', // A expiração é maior que hoje ou o campo de expiração está inativo
                        array(
                            'key'     => 'data_de_expiracao',
                            'value'   => date("Y-m-d"), // Define a data de hoje
                            'compare' => '>', // Exibe posts com data de expiração maior que hoje
                            'type'    => 'DATE' // Informa que estamos lidando com data
                        ),
                        array(
                            'key'     => 'o_banner_possui_data_de_expiracao',
                            'value'   => 'nao', // Exibe posts onde o campo de expiração está marcado como "não"
                            'compare' => '='
                        ),
                    ),
                ),
            );

            $banner_count = 0;
            $banner_query = new WP_Query($args);
            if ($banner_query->have_posts()) : while ($banner_query->have_posts()) : $banner_query->the_post();

            $desktop = get_field('banner_desktop');
            $mobile  = get_field('banner_mobile');

            if (!$desktop || !isset($desktop['url']) || !$mobile || !isset($mobile['url'])) continue;

            $link_externo   = get_field('possui_link_externo');
            $link_do_banner = get_field('link_do_banner');
            $target         = get_field('destino_do_link');
            $use_page       = get_field('usar_link_de_paginas_internas');
            $page_link      = get_field('selecione_a_pagina');

            $link = '';
            $targ = '';

            if ($link_externo) {
                if ($use_page === 'true') {
                    $link = ' href="' . esc_url(get_the_permalink($page_link)) . '"';
                } elseif ($link_do_banner) {
                    $link = ' href="' . esc_url($link_do_banner) . '"';
                } else {
                    $link = ' href="javascript:void(0)"';
                }
                $targ = ($target === '2') ? ' target="_blank"' : '';
            }

            // Otimização de LCP: Apenas o primeiro slide é prioritário
            $is_first       = ($banner_count === 0);
            $loading_attr   = $is_first ? 'eager' : 'lazy';
            $priority_attr  = $is_first ? 'fetchpriority="high"' : '';

            // Obtém IDs e SRCSet nativo do WordPress para melhor performance e economia de dados
            $desktop_id = $desktop['ID'] ?? null;
            $mobile_id  = $mobile['ID'] ?? null;

            $desktop_srcset = $desktop_id ? wp_get_attachment_image_srcset($desktop_id, 'full') : '';
            $mobile_srcset  = $mobile_id ? wp_get_attachment_image_srcset($mobile_id, 'large') : '';
            ?>

            <div class="swiper-slide">
                <a<?= $link . $targ; ?> title="<?= esc_attr(get_the_title()); ?>">
                    <picture data-swiper-parallax="-25%">
                        <source
                            srcset="<?= $desktop_srcset ? esc_attr($desktop_srcset) : esc_url($desktop['url']); ?>"
                            media="(min-width: 768px)"
                            sizes="100vw">
                        <img
                            src="<?= esc_url($mobile['url']); ?>"
                            <?php if ($mobile_srcset) : ?>srcset="<?= esc_attr($mobile_srcset); ?>"<?php endif; ?>
                            sizes="100vw"
                            alt="<?= esc_attr(get_the_title()); ?>"
                            class="img-fluid w-100"
                            loading="<?= $loading_attr; ?>"
                            <?= $priority_attr; ?>
                            decoding="async">
                    </picture>
                </a>
            </div>

            <?php
            $banner_count++;
            endwhile;
            endif;
            wp_reset_postdata(); ?>

        </div>

        <div class="swiper-pagination"></div>

        <div class="swiper-button-prev"><span class="icon-arrow_back_ios"></span></div>
        <div class="swiper-button-next"><span class="icon-arrow_forward_ios"></span></div>

    </div>

</div>

<script>
jQuery(document).ready(function ($) {

    // carousel do banner do topo
    new Swiper('#js-banner-swiper .swiper', {
        parallax: true,
        loop: true,

        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
            pauseOnMouseEnter: true,
        },

        pagination: {
            el: '#js-banner-swiper .swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '#js-banner-swiper .swiper-button-next',
            prevEl: '#js-banner-swiper .swiper-button-prev',
        },
    });
});
</script>
