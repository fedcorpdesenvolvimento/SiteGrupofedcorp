<?php
/**
 * Dados da empresa usados nas políticas e nos e-mails.
 * >>> PREENCHER os itens marcados com "PREENCHER" antes de divulgar as políticas. <<<
 * Os textos das páginas usam o shortcode [fedcorp_dado chave="..."], então basta
 * alterar aqui que todas as páginas são atualizadas.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fedcorp_lgpd_dados() {
	$dados = array(
		'razao_social'     => 'Fedcorp Administradora de Benefícios Ltda.',
		'nome_fantasia'    => 'Grupo Fedcorp',
		'cnpj'             => '35.315.360/0001-67',
		'endereco'         => 'Rua da Alfândega, 108, 7º andar, Centro, Rio de Janeiro/RJ',
		'endereco_sp'      => 'Rua Coronel Oscar Porto, 800, Vila Mariana, São Paulo/SP',
		'telefone'         => '(21) 2516-6001',
		'email_contato'    => 'sac@grupofedcorp.com.br',
		'dpo_nome'         => 'Alberto Cordeiro de Araujo',
		'dpo_email'        => 'alberto@grupofedcorp.com.br',
		'site'             => 'grupofedcorp.com.br',
		'atualizacao'      => '23/10/2026',                    // data da última revisão das políticas
		'versao_termos'    => '1.0',                           // ao mudar, usuários logados precisam aceitar de novo
		'prazo_resposta'   => '15 (quinze) dias',              // art. 19, II da LGPD
		'retencao_consent' => 2,                               // anos que o registro de consentimento de cookies fica guardado
	);
	return apply_filters( 'fedcorp_lgpd_dados', $dados );
}

function fedcorp_lgpd_dado( $chave ) {
	$d = fedcorp_lgpd_dados();
	return isset( $d[ $chave ] ) ? $d[ $chave ] : '';
}

add_shortcode( 'fedcorp_dado', function ( $atts ) {
	$a = shortcode_atts( array( 'chave' => '' ), $atts );
	return esc_html( fedcorp_lgpd_dado( $a['chave'] ) );
} );

/** Plataformas externas (abrem fora do site). Usadas nos termos e no aviso de primeiro acesso. */
function fedcorp_lgpd_plataformas() {
	return apply_filters( 'fedcorp_lgpd_plataformas', array(
		'incendiofedcorp.com.br'      => 'Plataforma de Incêndio Locação',
		'fedcorp.store'               => 'Plataforma Comercial',
		'multicalculofedcorp.com.br'  => 'Multicálculo de Condomínio',
		'plataforma.web.segimob.com'  => 'Cálculo de Seguro Fiança',
		'sinistro.grupofedcorp.com.br' => 'Sinistros Fedcorp',
		'produtosfedcorp.com'         => 'Marketplace de Produtos',
	) );
}

/** Hash de IP sem guardar o IP em si (minimização de dados). */
function fedcorp_lgpd_hash_ip() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : ''; // phpcs:ignore
	return substr( hash_hmac( 'sha256', $ip, wp_salt( 'auth' ) ), 0, 32 );
}
