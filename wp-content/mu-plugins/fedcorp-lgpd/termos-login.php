<?php
/**
 * Aceite dos Termos de Uso:
 *  a) Usuários do WordPress: no primeiro login (e sempre que a versão dos termos mudar)
 *     são levados à página /aceite-de-termos/ e só seguem após marcar o aceite.
 *  b) Visitantes que clicam em links das plataformas externas (Área Restrita, menu lateral):
 *     no primeiro clique aparece uma janela com o resumo dos termos; o aceite fica no navegador.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ---------- a) Usuários logados ---------- */
function fedcorp_termos_aceitos( $user_id ) {
	return get_user_meta( $user_id, 'fedcorp_termos_versao', true ) === fedcorp_lgpd_dado( 'versao_termos' );
}

add_action( 'template_redirect', 'fedcorp_termos_exigir', 1 );
add_action( 'admin_init', 'fedcorp_termos_exigir', 1 );
function fedcorp_termos_exigir() {
	if ( ! is_user_logged_in() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( ! empty( $_SERVER['SCRIPT_NAME'] ) && false !== strpos( $_SERVER['SCRIPT_NAME'], 'wp-login.php' ) ) { // phpcs:ignore
		return;
	}
	if ( fedcorp_termos_aceitos( get_current_user_id() ) ) {
		return;
	}
	$pagina = get_page_by_path( 'aceite-de-termos' );
	if ( ! $pagina ) {
		return;
	}
	if ( ! is_admin() && is_page( $pagina->ID ) ) {
		return;
	}
	$destino = isset( $_SERVER['REQUEST_URI'] ) ? esc_url_raw( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : admin_url(); // phpcs:ignore
	wp_safe_redirect( add_query_arg( 'redirect_to', rawurlencode( $destino ), get_permalink( $pagina ) ) );
	exit;
}

add_shortcode( 'fedcorp_aceite_termos', function () {
	if ( ! is_user_logged_in() ) {
		return '<p>Faça login para registrar o aceite.</p>';
	}
	$uid = get_current_user_id();
	$erro = '';
	if ( ! empty( $_POST['fc_aceite_enviar'] ) ) {
		if ( ! isset( $_POST['fc_aceite_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['fc_aceite_nonce'] ) ), 'fc_aceite_' . $uid ) ) {
			$erro = 'Sessão expirada. Recarregue a página.';
		} elseif ( empty( $_POST['fc_aceite_ok'] ) ) {
			$erro = 'É preciso marcar a caixa de aceite para continuar.';
		} else {
			update_user_meta( $uid, 'fedcorp_termos_versao', fedcorp_lgpd_dado( 'versao_termos' ) );
			update_user_meta( $uid, 'fedcorp_termos_data', current_time( 'mysql' ) );
			update_user_meta( $uid, 'fedcorp_termos_hash', fedcorp_lgpd_hash_ip() );
			$dest = isset( $_GET['redirect_to'] ) ? esc_url_raw( rawurldecode( wp_unslash( $_GET['redirect_to'] ) ) ) : admin_url(); // phpcs:ignore
			wp_safe_redirect( $dest ?: admin_url() );
			exit;
		}
	}
	if ( fedcorp_termos_aceitos( $uid ) ) {
		return '<p class="fc-legal__aviso">Você já aceitou a versão ' . esc_html( fedcorp_lgpd_dado( 'versao_termos' ) ) . ' dos Termos em ' . esc_html( get_user_meta( $uid, 'fedcorp_termos_data', true ) ) . '.</p>';
	}
	ob_start();
	?>
<form method="post" class="fc-form" style="background:#F3F6F9;border-radius:18px;padding:28px;margin:24px 0;font-family:Poppins,system-ui,sans-serif">
	<?php if ( $erro ) : ?><p class="fc-form__msg fc-form__msg--erro" role="alert" style="background:#FDE8E8;color:#842029;padding:12px 16px;border-radius:10px"><?php echo esc_html( $erro ); ?></p><?php endif; ?>
	<?php wp_nonce_field( 'fc_aceite_' . $uid, 'fc_aceite_nonce' ); ?>
	<p style="display:flex;gap:10px;align-items:flex-start;font-size:15px"><input type="checkbox" id="fc_aceite_ok" name="fc_aceite_ok" value="1" style="width:20px;height:20px;margin-top:3px;accent-color:#004077"><label for="fc_aceite_ok">Li e aceito os <strong>Termos de Uso</strong> (versão <?php echo esc_html( fedcorp_lgpd_dado( 'versao_termos' ) ); ?>) e a <strong>Política de Privacidade</strong> do Grupo Fedcorp.</label></p>
	<button type="submit" name="fc_aceite_enviar" value="1" style="cursor:pointer;border:0;border-radius:999px;padding:14px 28px;background:#004077;color:#fff;font:600 15px Poppins,system-ui,sans-serif">Aceitar e continuar</button>
</form>
	<?php
	return ob_get_clean();
} );

/* Coluna "Termos" na lista de usuários */
add_filter( 'manage_users_columns', function ( $c ) { $c['fc_termos'] = 'Termos aceitos'; return $c; } );
add_filter( 'manage_users_custom_column', function ( $out, $col, $uid ) {
	if ( 'fc_termos' === $col ) {
		$v = get_user_meta( $uid, 'fedcorp_termos_versao', true );
		return $v ? 'v' . esc_html( $v ) . ' em ' . esc_html( get_user_meta( $uid, 'fedcorp_termos_data', true ) ) : '—';
	}
	return $out;
}, 10, 3 );

/* ---------- b) Primeiro acesso às plataformas externas ---------- */
add_action( 'wp_footer', function () {
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore
		return;
	}
	$dominios = array_keys( fedcorp_lgpd_plataformas() );
	?>
<style id="fedcorp-termos-modal">
.fc-tm{ position:fixed; inset:0; z-index:99995; display:none; align-items:center; justify-content:center; padding:16px; background:rgba(0,20,40,.6); font-family:Poppins, system-ui, sans-serif; }
.fc-tm.is-aberto{ display:flex; }
.fc-tm__caixa{ background:#fff; color:#292929; max-width:560px; width:100%; border-radius:18px; padding:26px 28px; box-shadow:0 20px 60px rgba(0,30,60,.4); max-height:90vh; overflow:auto; }
.fc-tm__caixa h2{ margin:0 0 10px; color:#004077; font-size:20px; }
.fc-tm__caixa p, .fc-tm__caixa li{ font-size:15px; line-height:1.55; }
.fc-tm__caixa ul{ padding-left:20px; margin:8px 0 14px; }
.fc-tm__caixa a{ color:#1F6FA6; text-decoration:underline; }
.fc-tm__aceite{ display:flex; gap:10px; align-items:flex-start; margin:14px 0 18px; font-size:14.5px; }
.fc-tm__aceite input{ width:20px; height:20px; margin-top:2px; accent-color:#004077; }
.fc-tm__acoes{ display:flex; gap:10px; flex-wrap:wrap; }
.fc-tm__btn{ cursor:pointer; border:2px solid #004077; border-radius:999px; padding:11px 22px; font:600 14px Poppins, system-ui, sans-serif; background:#004077; color:#fff; }
.fc-tm__btn[disabled]{ opacity:.5; cursor:not-allowed; }
.fc-tm__btn--sec{ background:#fff; color:#004077; }
.fc-tm :focus-visible{ outline:3px solid #FFC203; outline-offset:2px; }
</style>
<div class="fc-tm" id="fc-tm" role="dialog" aria-modal="true" aria-labelledby="fc-tm-titulo" hidden>
	<div class="fc-tm__caixa">
		<h2 id="fc-tm-titulo">Antes de acessar a plataforma</h2>
		<p>Você está saindo do site institucional para uma plataforma do Grupo Fedcorp ou de um parceiro, com acesso por login. Ao continuar, você concorda que:</p>
		<ul>
			<li>o acesso é pessoal e intransferível, e você é responsável pelo sigilo das suas credenciais;</li>
			<li>os dados informados na plataforma são tratados conforme a <a href="<?php echo esc_url( fedcorp_lgpd_url( 'politica-de-privacidade' ) ); ?>" target="_blank" rel="noopener">Política de Privacidade</a> e os <a href="<?php echo esc_url( fedcorp_lgpd_url( 'termos-de-uso' ) ); ?>" target="_blank" rel="noopener">Termos de Uso</a> (versão <?php echo esc_html( fedcorp_lgpd_dado( 'versao_termos' ) ); ?>);</li>
			<li>plataformas de parceiros podem ter termos próprios, apresentados por eles.</li>
		</ul>
		<div class="fc-tm__aceite"><input type="checkbox" id="fc-tm-ok"><label for="fc-tm-ok">Li e aceito os Termos de Uso e a Política de Privacidade.</label></div>
		<div class="fc-tm__acoes">
			<button type="button" class="fc-tm__btn" id="fc-tm-continuar" disabled>Continuar para a plataforma</button>
			<button type="button" class="fc-tm__btn fc-tm__btn--sec" id="fc-tm-cancelar">Cancelar</button>
		</div>
	</div>
</div>
<script id="fedcorp-termos-modal-js">
(function(){
	var DOM=<?php echo wp_json_encode( $dominios ); ?>, CHAVE='fedcorp_termos_plataformas', VERSAO='<?php echo esc_js( fedcorp_lgpd_dado( 'versao_termos' ) ); ?>';
	var modal=document.getElementById('fc-tm'); if(!modal) return;
	var ok=document.getElementById('fc-tm-ok'), cont=document.getElementById('fc-tm-continuar'), canc=document.getElementById('fc-tm-cancelar'), pendente=null, foco=null;
	function aceito(){ try{ return localStorage.getItem(CHAVE)===VERSAO; }catch(e){ return false; } }
	function ehPlataforma(a){ try{ var h=new URL(a.href,location.href).hostname; return DOM.some(function(d){ return h===d||h.endsWith('.'+d); }); }catch(e){ return false; } }
	function abrir(a){ pendente=a; foco=document.activeElement; ok.checked=false; cont.disabled=true; modal.hidden=false; modal.classList.add('is-aberto'); ok.focus(); }
	function fechar(){ modal.classList.remove('is-aberto'); modal.hidden=true; pendente=null; if(foco&&foco.focus) foco.focus(); }
	document.addEventListener('click',function(e){
		var a=e.target.closest('a[href]'); if(!a||aceito()||!ehPlataforma(a)) return;
		e.preventDefault(); e.stopPropagation(); abrir(a);
	},true);
	ok.addEventListener('change',function(){ cont.disabled=!ok.checked; });
	cont.addEventListener('click',function(){ if(!ok.checked||!pendente) return; try{ localStorage.setItem(CHAVE,VERSAO); }catch(e){} var a=pendente; fechar(); if(a.target==='_blank'){ window.open(a.href,'_blank','noopener'); } else { location.href=a.href; } });
	canc.addEventListener('click',fechar);
	modal.addEventListener('keydown',function(e){ if(e.key==='Escape') fechar(); if(e.key==='Tab'){ var f=modal.querySelectorAll('a,button:not([disabled]),input'); var p=f[0], u=f[f.length-1]; if(e.shiftKey&&document.activeElement===p){ e.preventDefault(); u.focus(); } else if(!e.shiftKey&&document.activeElement===u){ e.preventDefault(); p.focus(); } } });
})();
</script>
	<?php
}, 45 );
