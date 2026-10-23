<?php
/**
 * Plugin Name: Fedcorp – LGPD, Privacidade e Conformidade
 * Description: Políticas (privacidade, cookies, termos, direitos autorais), aviso de cookies com Consent Mode v2, formulário de direitos do titular (LGPD), registro de consentimento, minimização de dados, aceite de termos no primeiro login e barra legal no rodapé.
 * Version:     1.0.0
 * Author:      Grupo Fedcorp
 *
 * Os textos e dados da empresa ficam em fedcorp-lgpd/config.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FEDCORP_LGPD_DIR', __DIR__ . '/fedcorp-lgpd/' );
define( 'FEDCORP_LGPD_VERSAO', '1.0.0' );

foreach ( array( 'config', 'paginas', 'cookies', 'formulario', 'minimizacao', 'termos-login', 'rodape' ) as $modulo ) {
	$arquivo = FEDCORP_LGPD_DIR . $modulo . '.php';
	if ( file_exists( $arquivo ) ) {
		require_once $arquivo;
	}
}
