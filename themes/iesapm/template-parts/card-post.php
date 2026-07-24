<div class="c-card-post h-100 row g-0 rounded-4 overflow-hidden">

    <div class="c-card-post__thumb col-12">
        <a href="<?php the_permalink(); ?>" class="text-decoration-none" title="<?php the_title(); ?>">
            <?php
            if ( has_post_thumbnail() ) {
                echo get_the_post_thumbnail( $post_id, 'large', array( 'class' => 'h-100 img-fluid object-fit-cover', 'style' => 'object-position: top;') );
            }else{
                echo wp_get_attachment_image( '2751', 'medium', "", ["class" => "h-100 w-100 img-fluid object-fit-cover"]);
            }
            ?>
        </a>
    </div>
    <div class="c-card-post__content p-2 col-12">
        <div class="gx-1 mb-2 row small">

            <div class="col-auto c-post-metadata">
                <span class="icon-calendar me-1 small text-dark"></span>
                <span class="small"><?php the_time('d/m/Y'); ?></span>
            </div>

            <?php if(has_category()): ?>
            <div class="col-auto c-post-metadata ms-3">
                <span class="icon-price-tag me-1 small text-dark"></span>
                <?php the_category(', '); ?>
            </div>
            <?php else : ?>
            <div class="align-items-center c-post-metadata col-auto d-inline-flex ms-3">
                <span class="icon-price-tag me-1 small text-dark"></span>

                <?php the_taxonomies( array(
                    'before' => '<div class="tax-link-wrap">',
                    'template' => '
                        <span class="taxonomy-label d-none">%s:</span>
                        <span class="taxonomy-term-list">%l</span>',
                    'term_template' => '<a href="%1$s" rel="tag">%2$s</a>',
                    'sep' => '<br />',
                    'after' => '</div>',
                ) ); ?>

            </div>
            <?php endif ?>

            <div class="col-auto c-post-metadata d-none">
                <span class="icon-price-tag me-1 small text-dark"></span>
                <span class="small"><?php the_author(); ?></span>
            </div>

        </div>
        <h3 class="h5">
            <a href="<?php the_permalink(); ?>" class="text-dark text-decoration-none" title="<?php the_title(); ?>">
                <?php the_title(); ?>
            </a>
        </h3>
        <!-- <p class="text-body"><?php // the_excerpt(); ?></p> -->
        <!-- <p>
            <a href="<?php //the_permalink(); ?>" class="text-primary text-decoration-none" title="<?php //the_title(); ?>">Leia mais >>></a>
        </p> -->
    </div>
</div>
