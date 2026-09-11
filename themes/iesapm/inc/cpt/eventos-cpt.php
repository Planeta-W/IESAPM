<?php
/*
* Custon post eventos.
*/

// post type
function eventos_cpt(){

    $labels = array(
		'name'                  => _x( 'Eventos', '', '' ),
		'singular_name'         => _x( 'Evento', '', '' ),
		'menu_name'             => __( 'Eventos', '' ),
		'all_items'             => __( 'Todos os itens', '' ),
		'add_new_item'          => __( 'Adicionar item', '' ),
		'add_new'               => __( 'Adicionar item', '' ),
		'new_item'              => __( 'Novo item', '' ),
		'edit_item'             => __( 'Editar item', '' ),
		'update_item'           => __( 'Atualizar item', '' ),
		'view_item'             => __( 'Ver item', '' ),
		'view_items'            => __( 'Ver todos os itens', '' ),
		'search_items'          => __( 'Buscar item', '' ),
		'not_found'             => __( 'Item não encontrado', '' ),
		'not_found_in_trash'    => __( 'Item não encontrado no lixo', '' ),
		'featured_image'        => __( 'Imagem destacada', '' ),
		'set_featured_image'    => __( 'Definir imagem', '' ),
		'remove_featured_image' => __( 'Remover imagem', '' ),
		'use_featured_image'    => __( 'Usar a imagem', '' ),
	);

    $supports = array(
        'title',
        'editor',
        'thumbnail',
        'author',
        'page-attributes'
    );

    $args = array(
		'label'                 => __( 'Eventos', '' ),
		'description'           => __( 'Eventos', '' ),
		'labels'                => $labels,
		'supports'              => $supports,
		//'taxonomies'            => array( ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'show_in_rest'          => true,
		//'menu_position'         => 26,
		'menu_icon'             => 'dashicons-admin-page',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'post',
        'rewrite'               => array( 'slug' => 'eventos' ), // my custom slug
	);
	register_post_type( 'eventos', $args );
}
add_action( 'init', 'eventos_cpt', 0 );

?>
