<?php
/**
 * Formulário de direitos do titular (LGPD, art. 18) – shortcode [fedcorp_lgpd_form]
 * Guarda cada pedido como "Solicitação LGPD" (privada, menu Ferramentas) com número de protocolo,
 * envia e-mail ao Encarregado e confirmação ao solicitante.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function () {
	register_post_type( 'fc_solicitacao', array(
		'labels'          => array( 'name' => 'Solicitações LGPD', 'singular_name' => 'Solicitação LGPD' ),
		'public'          => false,
		'show_ui'         => true,
		'show_in_menu'    => 'tools.php',
		'supports'        => array( 'title', 'editor', 'custom-fields' ),
		'capability_type' => 'post',
		'capabilities'    => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap'    => true,
	) );
} );

function fedcorp_lgpd_tipos_pedido() {
	return array(
		'acesso'        => 'Confirmação de tratamento e acesso aos meus dados',
		'correcao'      => 'Correção de dados incompletos, inexatos ou desatualizados',
		'eliminacao'    => 'Anonimização, bloqueio ou eliminação de dados',
		'portabilidade' => 'Portabilidade dos dados',
		'compartilhamento' => 'Informação sobre compartilhamento com terceiros',
		'revogacao'     => 'Revogação do consentimento',
		'oposicao'      => 'Oposição a um tratamento',
		'outro'         => 'Outro',
	);
}

add_shortcode( 'fedcorp_lgpd_form', 'fedcorp_lgpd_form_render' );
function fedcorp_lgpd_form_render() {
	$resultado = fedcorp_lgpd_form_processar();
	ob_start();
	?>
<style id="fedcorp-lgpd-form">
.fc-form{ background:#F3F6F9; border-radius:18px; padding:28px; margin:28px 0; }
.fc-form__campo{ margin-bottom:16px; }
.fc-form label{ display:block; font-weight:600; color:#004077; margin-bottom:6px; font-size:15px; }
.fc-form input[type=text], .fc-form input[type=email], .fc-form select, .fc-form textarea{ width:100%; box-sizing:border-box; padding:12px 14px; border:1px solid #c9d3de; border-radius:10px; font:16px Poppins, system-ui, sans-serif; background:#fff; color:#292929; }
.fc-form input:focus-visible, .fc-form select:focus-visible, .fc-form textarea:focus-visible, .fc-form button:focus-visible{ outline:3px solid #FFC203; outline-offset:2px; }
.fc-form textarea{ min-height:140px; }
.fc-form__aceite{ display:flex; gap:10px; align-items:flex-start; font-size:14.5px; }
.fc-form__aceite input{ width:20px; height:20px; margin-top:2px; accent-color:#004077; }
.fc-form__aceite label{ font-weight:400; color:#292929; margin:0; }
.fc-form button{ cursor:pointer; border:0; border-radius:999px; padding:14px 28px; background:#004077; color:#fff; font:600 15px Poppins, system-ui, sans-serif; }
.fc-form button:hover{ background:#002d54; }
.fc-form__msg{ padding:14px 18px; border-radius:10px; margin-bottom:18px; font-size:15px; }
.fc-form__msg--ok{ background:#E3F4EA; color:#0F5132; border-left:4px solid #1B8A4C; }
.fc-form__msg--erro{ background:#FDE8E8; color:#842029; border-left:4px solid #C62828; }
.fc-form__dica{ font-size:13px; color:#555; margin-top:4px; }
.fc-form .fc-hp{ position:absolute; left:-9999px; }
</style>
<form class="fc-form" method="post" action="#fc-lgpd-form" id="fc-lgpd-form" novalidate>
	<?php if ( $resultado ) : ?>
		<div class="fc-form__msg fc-form__msg--<?php echo $resultado['ok'] ? 'ok' : 'erro'; ?>" role="alert"><?php echo wp_kses_post( $resultado['msg'] ); ?></div>
	<?php endif; ?>
	<?php wp_nonce_field( 'fc_lgpd_form', 'fc_lgpd_nonce' ); ?>
	<div class="fc-hp" aria-hidden="true"><label for="fc_site">Deixe em branco</label><input type="text" id="fc_site" name="fc_site" tabindex="-1" autocomplete="off"></div>
	<div class="fc-form__campo"><label for="fc_nome">Nome completo <span aria-hidden="true">*</span></label><input type="text" id="fc_nome" name="fc_nome" required autocomplete="name" maxlength="120"></div>
	<div class="fc-form__campo"><label for="fc_email">E-mail para resposta <span aria-hidden="true">*</span></label><input type="email" id="fc_email" name="fc_email" required autocomplete="email" maxlength="160"><p class="fc-form__dica">Use, se possível, o mesmo e-mail que você informou ao Fedcorp, para facilitar a localização dos dados.</p></div>
	<div class="fc-form__campo"><label for="fc_relacao">Sua relação com o Fedcorp</label><select id="fc_relacao" name="fc_relacao">
		<option value="">Selecione (opcional)</option>
		<?php foreach ( array( 'Cliente / administradora / imobiliária', 'Condômino, inquilino ou proprietário', 'Segurado ou beneficiário', 'Colaborador ou candidato', 'Fornecedor ou parceiro', 'Visitante do site', 'Outro' ) as $o ) : ?>
			<option value="<?php echo esc_attr( $o ); ?>"><?php echo esc_html( $o ); ?></option>
		<?php endforeach; ?>
	</select></div>
	<div class="fc-form__campo"><label for="fc_tipo">Tipo de solicitação <span aria-hidden="true">*</span></label><select id="fc_tipo" name="fc_tipo" required>
		<option value="">Selecione</option>
		<?php foreach ( fedcorp_lgpd_tipos_pedido() as $k => $v ) : ?>
			<option value="<?php echo esc_attr( $k ); ?>"><?php echo esc_html( $v ); ?></option>
		<?php endforeach; ?>
	</select></div>
	<div class="fc-form__campo"><label for="fc_msg">Descreva o pedido <span aria-hidden="true">*</span></label><textarea id="fc_msg" name="fc_msg" required maxlength="3000"></textarea><p class="fc-form__dica">Não inclua senhas, números de cartão ou dados de saúde. Pediremos documentos somente se forem necessários para confirmar sua identidade.</p></div>
	<div class="fc-form__campo fc-form__aceite"><input type="checkbox" id="fc_aceite" name="fc_aceite" value="1" required><label for="fc_aceite">Declaro que li a <a href="<?php echo esc_url( fedcorp_lgpd_url( 'politica-de-privacidade' ) ); ?>" target="_blank" rel="noopener">Política de Privacidade</a> e autorizo o uso dos dados acima exclusivamente para atender a esta solicitação. <span aria-hidden="true">*</span></label></div>
	<button type="submit" name="fc_lgpd_enviar" value="1">Enviar solicitação</button>
</form>
	<?php
	return ob_get_clean();
}

function fedcorp_lgpd_form_processar() {
	if ( empty( $_POST['fc_lgpd_enviar'] ) ) {
		return null;
	}
	if ( ! isset( $_POST['fc_lgpd_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fc_lgpd_nonce'] ) ), 'fc_lgpd_form' ) ) {
		return array( 'ok' => false, 'msg' => 'Sessão expirada. Recarregue a página e tente novamente.' );
	}
	if ( ! empty( $_POST['fc_site'] ) ) { // honeypot
		return array( 'ok' => true, 'msg' => 'Solicitação recebida.' );
	}
	$nome   = sanitize_text_field( wp_unslash( $_POST['fc_nome'] ?? '' ) );
	$email  = sanitize_email( wp_unslash( $_POST['fc_email'] ?? '' ) );
	$rel    = sanitize_text_field( wp_unslash( $_POST['fc_relacao'] ?? '' ) );
	$tipo   = sanitize_key( $_POST['fc_tipo'] ?? '' );
	$msg    = sanitize_textarea_field( wp_unslash( $_POST['fc_msg'] ?? '' ) );
	$aceite = ! empty( $_POST['fc_aceite'] );
	$tipos  = fedcorp_lgpd_tipos_pedido();
	$faltas = array();
	if ( ! $nome ) { $faltas[] = 'nome completo'; }
	if ( ! is_email( $email ) ) { $faltas[] = 'e-mail válido'; }
	if ( ! isset( $tipos[ $tipo ] ) ) { $faltas[] = 'tipo de solicitação'; }
	if ( mb_strlen( $msg ) < 10 ) { $faltas[] = 'descrição com pelo menos 10 caracteres'; }
	if ( ! $aceite ) { $faltas[] = 'a declaração de leitura da Política de Privacidade'; }
	if ( $faltas ) {
		return array( 'ok' => false, 'msg' => 'Verifique: ' . implode( '; ', $faltas ) . '.' );
	}
	// Limite: 3 pedidos por hora por hash de IP
	$chave = 'fc_sol_' . fedcorp_lgpd_hash_ip();
	$n = (int) get_transient( $chave );
	if ( $n >= 3 ) {
		return array( 'ok' => false, 'msg' => 'Muitas solicitações em pouco tempo. Tente novamente mais tarde ou escreva para ' . esc_html( fedcorp_lgpd_dado( 'dpo_email' ) ) . '.' );
	}
	set_transient( $chave, $n + 1, HOUR_IN_SECONDS );

	$protocolo = 'LGPD-' . current_time( 'Ymd' ) . '-' . strtoupper( wp_generate_password( 5, false ) );
	$prazo     = date_i18n( 'd/m/Y', strtotime( '+15 days', current_time( 'timestamp' ) ) );
	$id = wp_insert_post( array(
		'post_type'    => 'fc_solicitacao',
		'post_status'  => 'private',
		'post_title'   => $protocolo . ' · ' . $tipos[ $tipo ] . ' · ' . $nome,
		'post_content' => $msg,
		'meta_input'   => array( 'nome' => $nome, 'email' => $email, 'relacao' => $rel, 'tipo' => $tipo, 'protocolo' => $protocolo, 'prazo' => $prazo, 'status' => 'aberto', 'hash' => fedcorp_lgpd_hash_ip() ),
	) );
	if ( ! $id ) {
		return array( 'ok' => false, 'msg' => 'Não foi possível registrar o pedido. Escreva para ' . esc_html( fedcorp_lgpd_dado( 'dpo_email' ) ) . '.' );
	}
	$dpo  = fedcorp_lgpd_dado( 'dpo_email' );
	$site = get_bloginfo( 'name' );
	$corpo_dpo = "Nova solicitação de titular de dados (LGPD)\n\nProtocolo: $protocolo\nPrazo legal de resposta (15 dias): $prazo\nTipo: {$tipos[$tipo]}\nNome: $nome\nE-mail: $email\nRelação: $rel\n\nDescrição:\n$msg\n\nGerenciar: " . admin_url( 'post.php?post=' . $id . '&action=edit' );
	wp_mail( $dpo, "[$site] Solicitação LGPD $protocolo – {$tipos[$tipo]}", $corpo_dpo, array( 'Reply-To: ' . $email ) );
	$corpo_tit = "Olá, $nome.\n\nRecebemos sua solicitação de \"{$tipos[$tipo]}\".\nProtocolo: $protocolo\nResponderemos até $prazo, conforme a LGPD (art. 19).\n\nSe precisarmos confirmar sua identidade, entraremos em contato por este e-mail.\n\nEncarregado de Proteção de Dados – $site\n$dpo";
	wp_mail( $email, "[$site] Recebemos sua solicitação LGPD – $protocolo", $corpo_tit );
	return array( 'ok' => true, 'msg' => '<strong>Solicitação registrada.</strong> Protocolo <strong>' . esc_html( $protocolo ) . '</strong>. Enviamos uma confirmação para ' . esc_html( $email ) . ' e responderemos até ' . esc_html( $prazo ) . '.' );
}

/* Colunas úteis na listagem do admin */
add_filter( 'manage_fc_solicitacao_posts_columns', function ( $c ) {
	return array( 'cb' => $c['cb'], 'title' => 'Solicitação', 'fc_email' => 'E-mail', 'fc_prazo' => 'Prazo', 'fc_status' => 'Status', 'date' => 'Recebida em' );
} );
add_action( 'manage_fc_solicitacao_posts_custom_column', function ( $col, $id ) {
	if ( 'fc_email' === $col ) { echo esc_html( get_post_meta( $id, 'email', true ) ); }
	if ( 'fc_prazo' === $col ) { echo esc_html( get_post_meta( $id, 'prazo', true ) ); }
	if ( 'fc_status' === $col ) { echo esc_html( get_post_meta( $id, 'status', true ) ?: 'aberto' ); }
}, 10, 2 );
