<?php
/**
 * Minimização de dados e redução de superfície de exposição (LGPD art. 6º, III e VII).
 * - Desliga comentários e pingbacks (o site não os usa) e para de gravar IP de comentários.
 * - Não expõe lista de usuários pela API REST nem por ?author=N para visitantes.
 * - Desativa XML-RPC (vetor de ataque e de vazamento) e oculta a versão do WordPress.
 * - Não permite registro público de usuários.
 * - Mantém o sitemap/SEO intactos.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Comentários e pingbacks */
add_action( 'init', function () {
	foreach ( get_post_types() as $pt ) {
		remove_post_type_support( $pt, 'comments' );
		remove_post_type_support( $pt, 'trackbacks' );
	}
} );
add_filter( 'comments_open', '__return_false', 20 );
add_filter( 'pings_open', '__return_false', 20 );
add_filter( 'comments_array', '__return_empty_array', 20 );
add_filter( 'pre_comment_user_ip', '__return_empty_string' );
add_action( 'admin_menu', function () { remove_menu_page( 'edit-comments.php' ); } );
add_action( 'wp_before_admin_bar_render', function () { global $wp_admin_bar; $wp_admin_bar->remove_menu( 'comments' ); } );
add_filter( 'pre_option_default_comment_status', function () { return 'closed'; } );
add_filter( 'pre_option_default_ping_status', function () { return 'closed'; } );

/* Usuários: sem enumeração */
add_filter( 'rest_endpoints', function ( $endpoints ) {
	if ( ! is_user_logged_in() ) {
		unset( $endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)'] );
	}
	return $endpoints;
} );
add_action( 'template_redirect', function () {
	if ( ! is_admin() && isset( $_GET['author'] ) && ! is_user_logged_in() ) { // phpcs:ignore
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
} );
add_filter( 'pre_option_users_can_register', '__return_zero' );

/* Superfície: XML-RPC, versão, cabeçalhos */
add_filter( 'xmlrpc_enabled', '__return_false' );
add_filter( 'wp_headers', function ( $h ) { unset( $h['X-Pingback'] ); return $h; } );
remove_action( 'wp_head', 'wp_generator' );
add_filter( 'the_generator', '__return_empty_string' );
add_action( 'send_headers', function () {
	if ( headers_sent() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
} );

/* Meta de direitos autorais e política de privacidade no <head> */
add_action( 'wp_head', function () {
	echo '<meta name="copyright" content="© ' . esc_attr( gmdate( 'Y' ) . ' ' . fedcorp_lgpd_dado( 'razao_social' ) ) . '. Todos os direitos reservados.">' . "\n";
	$pp = get_option( 'wp_page_for_privacy_policy' );
	if ( $pp ) {
		echo '<link rel="privacy-policy" href="' . esc_url( get_permalink( $pp ) ) . '">' . "\n";
	}
}, 2 );

/* Retenção automática: solicitações LGPD concluídas há mais de 5 anos */
add_action( 'fedcorp_lgpd_limpeza', function () {
	$velhos = get_posts( array( 'post_type' => 'fc_solicitacao', 'post_status' => 'any', 'posts_per_page' => 200, 'fields' => 'ids', 'date_query' => array( array( 'before' => '5 years ago' ) ) ) );
	foreach ( $velhos as $id ) {
		wp_delete_post( $id, true );
	}
} );
