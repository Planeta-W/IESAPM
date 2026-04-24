<?php
$card_link = get_the_permalink();
$link_target = '';

// Verifica se o link deve ser externo
if ( get_field( 'verifica_link_externo' ) == '1' ) {
    $externo = get_field( 'link_externo' );
    if ( $externo ) {
        $card_link   = $externo;
        $link_target = ' target="_blank" rel="noopener noreferrer"';
    }
}
echo '<!-- Link interno: ' . get_field( 'verifica_link_externo' ) . ' -->';
echo '<!-- Link externo: ' . esc_url( $card_link ) . ' -->';
?>
<div class="c-card-curso bg-primary h-100 rounded-4 overflow-auto">
    <div class="bg-primary h-100">
        <a href="<?php echo esc_url( $card_link ); ?>" class="text-decoration-none" title="<?php the_title_attribute(); ?>"<?php echo $link_target; ?>>
            <figure class="c-card-curso__figure">
                <?php the_post_thumbnail( 'full', array( 'class' => 'img-fluid w-100' ) ); ?>
            </figure>
            <div class="bg-primary c-card-curso__wrapper p-3">
                <h3 class="fs-5 lh-base mb-4 text-center text-white"><?php the_title(); ?></h3>
                <p class="fs-6 fw-bold text-center text-white">Saiba mais</p>
            </div>
        </a>
    </div>
</div>
