<?php
/**
 * Footer
 *
 * @package WordPress
 * @subpackage iesapm
 *
 */

?>

<footer class="footer-site">
    <div class="footer-info bg-white">
        <div class="container">
            <div class="row gy-5 justify-content-around py-5">
                <div class="col-12 col-xl-auto">
                    <p class="fw-bold text-center">
                        <a href="https://emec.mec.gov.br/emec/consulta-cadastro/detalhamento/d96957f455f6405d14c6542552b0f6eb/MjQwNDY=" class="text-decoration-none" target="_blank" title="Nosso cadastro no sistema e-mec">Clique aqui e acesse nosso cadastro no sistema e-mec</a>
                    </p>
                    <div style="max-width: 150px" class="mx-auto">
                        <img src="<?php bloginfo('wpurl')?>/wp-content/uploads/qr-code-mec.jpg" alt="<?php bloginfo('name')?>" class="img-fluid">
                    </div>
                </div>
                <div class="col-auto">
                    <div class="bl-contatos h-100 px-lg-5">
                        <address class="d-flex mb-3">
                            <div class="me-3">
                                <img src="<?php bloginfo('wpurl')?>/wp-content/uploads/icon-endereco.png" alt="Endereço">
                            </div>
                            <div style="max-width: 245px;">
                                <a href="https://goo.gl/maps/GrjuUxnFsUV6hEND9" title="Endereço" target="_blank" class="text-dark text-decoration-none small">Av. Brigadeiro Luís Antônio, 278 Bela Vista - São Paulo/SP</a>
                            </div>
                        </address>
                        <div class="d-flex mb-3 align-items-center">
                            <div class="me-3">
                                <img src="<?php bloginfo('wpurl')?>/wp-content/uploads/icon-tel.png" alt="Telefone">
                            </div>
                            <div>
                              <span class="icon-phone me-1 text-primary"></span>
                                <a href="tel:+551131884300" title="Telefone" class="text-dark text-decoration-none small">11 3188-4300</a> <br>
                                <i class="fa-brands fa-whatsapp me-1 text-primary"></i>
                                <a class="text-decoration-none text-dark small" href="https://wa.me/+5511975195075" title="Contato: Inscrições e Matrículas" target="_blank">11 9 7519-5075</a>
                            </div>
                        </div>
                        <div class="d-flex">
                            <div class="me-3">
                                <img src="<?php bloginfo('wpurl')?>/wp-content/uploads/icon-email.png" alt="Email">
                            </div>
                            <div>
                                <a href="mailto:comercial@iesapm.org.br" title="Email" class="text-dark text-decoration-none small">comercial@iesapm.org.br</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-auto">
                    <div class="d-flex mb-5">
                        <?php get_template_part('template-parts/rede-social'); ?>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="<?php echo get_permalink(46); ?>" class="btn btn--sm btn-primary mb-3" title="Ouvidoria">Ouvidoria</a>
                        <a href="<?php echo get_permalink(3); ?>" class="btn btn--sm btn-primary" title="Política de Privacidade">Política de Privacidade</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bg-primary py-3">
        <div class="container">
            <div class="align-items-center justify-content-center row">

                <div class="col-auto col-md">
                    <p class="mb-0 small text-center text-white">Copyright - Instituto de Ensino Superior da Associação Paulista de Medicina -  Todos os direitos reservados - <?php the_time('Y'); ?></p>
                </div>

                <div class="col-auto bl-pw mt-3 mt-md-0">
                    <a href="https://planetaw.ag/" title="Planeta W - Design + Web" target="_blank">
                        <img src="<?php echo get_bloginfo( 'wpurl' ); ?>/wp-content/uploads/pw.png" alt="Planeta W - Design + Web" class="img-fluid">
                    </a>
                </div>

            </div>
        </div>
    </div>
</footer>

</div> <!-- // wrapper site -->

<?php wp_footer(); ?>

<!-- <script type="text/javascript" async src="https://d335luupugsy2.cloudfront.net/js/loader-scripts/447d04a9-bdba-421a-930e-e8ea85741a7a-loader.js" ></script> -->

<script type="text/javascript" async src="https://d335luupugsy2.cloudfront.net/js/loader-scripts/6c990565-2071-44f9-b366-0b67f913d8a8-loader.js" ></script>

</body>
</html>
