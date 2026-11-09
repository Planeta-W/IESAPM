<?php
/**
 * Plugin Name: Local Dev
 * Description: Ajustes só do ambiente Docker local (montado por docker-compose.yml). Não grava nada no banco.
 */

// Desativa plugins que atrapalham em localhost, sem alterar a lista salva no banco.
// All-In-One Security: captcha Cloudflare Turnstile no login do admin.
add_filter( 'option_active_plugins', function ( $plugins ) {
	$bloqueados = array( 'all-in-one-wp-security-and-firewall/wp-security.php' );
	return is_array( $plugins ) ? array_values( array_diff( $plugins, $bloqueados ) ) : $plugins;
} );
