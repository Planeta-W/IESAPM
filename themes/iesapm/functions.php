<?php
/*--------------------------------------------------------------
ESTILOS E SCRIPTS
--------------------------------------------------------------*/
function enqueue_scripts() {

	// VERSÃO DO TEMA
	$tema_version = '1.3';

	// OWL CAROUSEL 2
	if ( is_front_page() ) {
		wp_enqueue_style( 'owlcarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.css', array(), '2.3.4' );
		wp_enqueue_script( 'owlcarousel', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', array( 'jquery' ), '2.3.4', true );
	}

	// SWIPER (banner do topo)
	if ( is_front_page() ) {
		wp_enqueue_style( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.1.4/swiper-bundle.min.css', array(), '11.1.4' );
		wp_enqueue_script( 'swiper', 'https://cdnjs.cloudflare.com/ajax/libs/Swiper/11.1.4/swiper-bundle.min.js', array(), '11.1.4', true );
	}

	// Lightbox2
	if ( is_page( 61 ) ) {
		wp_enqueue_style( 'lightbox2', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/css/lightbox.min.css', array(), '2.11.3' );
		wp_enqueue_script( 'lightbox2', 'https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js', array( 'jquery' ), '2.11.3', true );
	}

	// jQuery UI (Datepicker)
	if ( is_page( 44 ) ) {
		wp_enqueue_style( 'jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css', array(), '1.13.2' );
		wp_enqueue_script( 'jquery-ui', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', array( 'jquery' ), '1.13.2', true );
	}

	// FontAwesome
	wp_enqueue_style( 'fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css', array(), '6.4.2' );

	// THEME CSS
	wp_enqueue_style( 'tema', get_template_directory_uri() . '/css/theme.min.css', array(), $tema_version );

	// THEME JS
	wp_enqueue_script( 'tema', get_template_directory_uri() . '/js/theme.js', array( 'jquery' ), $tema_version, true );

	// CSS DEFAULT
	wp_enqueue_style( 'default-style', get_stylesheet_uri(), array(), $tema_version );

}
add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );


/*--------------------------------------------------------------
CONFIGURACOES DO TEMA
--------------------------------------------------------------*/

function setup_theme() {

	// Adicionar title dinamicamente
	add_theme_support('title-tag');

	// // Esconder barra logada wordpress
	// add_filter('show_admin_bar', '__return_false');

	// Habilitar imagem destacada
	add_theme_support('post-thumbnails');

	// habilitar resumo em páginas
	add_post_type_support( 'page', 'excerpt' );

	// habilitar responsivo em todos os embeds (youtube)
	add_theme_support( 'responsive-embeds' );

	// Registrar menus
	register_nav_menus( array(
		'menu-principal' => 'Menu principal'
	) );
}

add_action( 'after_setup_theme', 'setup_theme' );


/*--------------------------------------------------------------
DEFAULT
--------------------------------------------------------------*/
require_once get_template_directory() . '/inc/default.php';


/*--------------------------------------------------------------
CPT
--------------------------------------------------------------*/
include ('inc/cpt/graduacao-cpt.php');
include ('inc/cpt/pos-graduacao-cpt.php');
include ('inc/cpt/extensao-cpt.php');
include ('inc/cpt/corpo-docente-cpt.php');

/*--------------------------------------------------------------
Pre_get_posts
--------------------------------------------------------------*/
include ('inc/pre_get_posts.php');

?>
