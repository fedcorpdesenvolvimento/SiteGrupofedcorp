<?php
/**
 * Aviso de cookies + Google Consent Mode v2 + bloqueio de scripts até o consentimento
 * + registro de consentimento (prova) via REST.
 *
 * Scripts de terceiros que dependem de consentimento devem ser inseridos como:
 *   <script type="text/plain" data-fc-consent="statistics"> ... </script>
 *   <script type="text/plain" data-fc-consent="marketing" src="..."></script>
 * Eles só são executados após o aceite da categoria.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FEDCORP_COOKIE_NOME', 'fedcorp_consent' );
define( 'FEDCORP_COOKIE_DIAS', 180 );

/* 1) Consent Mode v2: padrão "negado" ANTES de qualquer tag do Google (Site Kit imprime em wp_head). */
add_action( 'wp_head', function () {
	if ( is_admin() ) {
		return;
	}
	?>
<script id="fedcorp-consent-default">
window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}
(function(){
	var c=null;try{var m=document.cookie.match(/(?:^|; )<?php echo FEDCORP_COOKIE_NOME; ?>=([^;]*)/);c=m?JSON.parse(decodeURIComponent(m[1])):null;}catch(e){}
	var st=c&&c.statistics?'granted':'denied', mk=c&&c.marketing?'granted':'denied';
	gtag('consent','default',{ad_storage:mk,ad_user_data:mk,ad_personalization:mk,analytics_storage:st,functionality_storage:'granted',security_storage:'granted',wait_for_update:500});
	gtag('set','ads_data_redaction',true); gtag('set','url_passthrough',false);
	window.fedcorpConsent={get:function(){return c;}};
})();
</script>
	<?php
}, -1000 );

/* 2) Banner + painel de preferências */
add_action( 'wp_footer', 'fedcorp_cookies_banner', 40 );
function fedcorp_cookies_banner() {
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore
		return;
	}
	$url_cookies = fedcorp_lgpd_url( 'politica-de-cookies' );
	$url_priv    = fedcorp_lgpd_url( 'politica-de-privacidade' );
	?>
<style id="fedcorp-cookies">
.fc-ck{ position:fixed; left:16px; right:16px; bottom:16px; z-index:99990; font-family:Poppins, system-ui, sans-serif; display:none; }
.fc-ck.is-aberto{ display:block; }
.fc-ck__caixa{ max-width:720px; margin:0 auto; background:#fff; color:#292929; border-radius:18px; box-shadow:0 18px 50px rgba(0,30,60,.3); padding:22px 24px; border:1px solid #e3e8ee; }
.fc-ck__titulo{ margin:0 0 6px; font-size:18px; font-weight:600; color:#004077; }
.fc-ck__texto{ margin:0 0 16px; font-size:15px; line-height:1.55; }
.fc-ck__texto a{ color:#1F6FA6; text-decoration:underline; }
.fc-ck__acoes{ display:flex; flex-wrap:wrap; gap:10px; align-items:center; }
.fc-ck__btn{ cursor:pointer; border:2px solid #004077; border-radius:999px; padding:10px 20px; font:600 14px/1 Poppins, system-ui, sans-serif; background:#004077; color:#fff; transition:background .2s, color .2s, transform .2s; }
.fc-ck__btn:hover, .fc-ck__btn:focus-visible{ background:#002d54; border-color:#002d54; transform:translateY(-1px); }
.fc-ck__btn--sec{ background:#fff; color:#004077; }
.fc-ck__btn--sec:hover, .fc-ck__btn--sec:focus-visible{ background:#F3F6F9; color:#004077; }
.fc-ck__btn--link{ background:none; border-color:transparent; color:#1F6FA6; text-decoration:underline; padding:10px 8px; }
.fc-ck__btn--link:hover, .fc-ck__btn--link:focus-visible{ background:none; border-color:transparent; color:#004077; }
.fc-ck :focus-visible{ outline:3px solid #FFC203; outline-offset:2px; }
.fc-ck__prefs{ display:none; margin:6px 0 16px; border-top:1px solid #e3e8ee; padding-top:14px; }
.fc-ck.is-prefs .fc-ck__prefs{ display:block; }
.fc-ck__cat{ display:flex; gap:14px; align-items:flex-start; padding:10px 0; border-bottom:1px dashed #e3e8ee; }
.fc-ck__cat:last-child{ border-bottom:0; }
.fc-ck__cat label{ font-weight:600; color:#004077; font-size:15px; display:block; cursor:pointer; }
.fc-ck__cat p{ margin:2px 0 0; font-size:13.5px; color:#555; line-height:1.5; }
.fc-ck__cat input{ width:20px; height:20px; margin-top:3px; accent-color:#004077; flex:0 0 auto; }
@media (max-width:600px){ .fc-ck{ left:8px; right:8px; bottom:8px; } .fc-ck__caixa{ padding:18px; } .fc-ck__btn{ width:100%; text-align:center; } }
@media (max-width:767px){ .fc-ck{ bottom:76px; } }
</style>
<div class="fc-ck" id="fc-ck" role="dialog" aria-modal="false" aria-labelledby="fc-ck-titulo" aria-describedby="fc-ck-texto" hidden>
	<div class="fc-ck__caixa">
		<h2 class="fc-ck__titulo" id="fc-ck-titulo">Privacidade e cookies</h2>
		<p class="fc-ck__texto" id="fc-ck-texto">Usamos cookies necessários para o site funcionar e, com a sua permissão, cookies de estatística para entender como ele é usado. Você pode mudar de ideia quando quiser. Saiba mais na <a href="<?php echo esc_url( $url_cookies ); ?>">Política de Cookies</a> e na <a href="<?php echo esc_url( $url_priv ); ?>">Política de Privacidade</a>.</p>
		<div class="fc-ck__prefs" id="fc-ck-prefs">
			<div class="fc-ck__cat"><input type="checkbox" id="fc-ck-nec" checked disabled><div><label for="fc-ck-nec">Necessários (sempre ativos)</label><p>Garantem o funcionamento básico, a segurança e a memorização das suas escolhas.</p></div></div>
			<div class="fc-ck__cat"><input type="checkbox" id="fc-ck-stat"><div><label for="fc-ck-stat">Estatísticas</label><p>Google Analytics 4, com dados agregados, para medir audiência e melhorar o site.</p></div></div>
			<div class="fc-ck__cat"><input type="checkbox" id="fc-ck-mkt"><div><label for="fc-ck-mkt">Marketing</label><p>Não utilizamos no momento. Se passarmos a utilizar, só serão ativados com a sua permissão.</p></div></div>
		</div>
		<div class="fc-ck__acoes">
			<button type="button" class="fc-ck__btn" data-fc-acao="aceitar">Aceitar todos</button>
			<button type="button" class="fc-ck__btn fc-ck__btn--sec" data-fc-acao="necessarios">Apenas necessários</button>
			<button type="button" class="fc-ck__btn fc-ck__btn--link" data-fc-acao="prefs" aria-expanded="false" aria-controls="fc-ck-prefs">Preferências</button>
			<button type="button" class="fc-ck__btn fc-ck__btn--sec" data-fc-acao="salvar" hidden>Salvar preferências</button>
		</div>
	</div>
</div>
<script id="fedcorp-cookies-js">
(function(){
	var NOME='<?php echo FEDCORP_COOKIE_NOME; ?>', DIAS=<?php echo (int) FEDCORP_COOKIE_DIAS; ?>, REST='<?php echo esc_url( rest_url( 'fedcorp/v1/consentimento' ) ); ?>';
	var el=document.getElementById('fc-ck'); if(!el) return;
	var stat=document.getElementById('fc-ck-stat'), mkt=document.getElementById('fc-ck-mkt');
	var btnPrefs=el.querySelector('[data-fc-acao="prefs"]'), btnSalvar=el.querySelector('[data-fc-acao="salvar"]');
	function ler(){ try{ var m=document.cookie.match(new RegExp('(?:^|; )'+NOME+'=([^;]*)')); return m?JSON.parse(decodeURIComponent(m[1])):null; }catch(e){ return null; } }
	function gravar(c){ var d=new Date(); d.setTime(d.getTime()+DIAS*864e5); document.cookie=NOME+'='+encodeURIComponent(JSON.stringify(c))+'; expires='+d.toUTCString()+'; path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':''); }
	function aplicar(c){
		var st=c.statistics?'granted':'denied', mk=c.marketing?'granted':'denied';
		if(typeof gtag==='function'){ gtag('consent','update',{analytics_storage:st,ad_storage:mk,ad_user_data:mk,ad_personalization:mk}); }
		if(window.wp_set_consent){ try{ wp_set_consent('statistics',c.statistics?'allow':'deny'); wp_set_consent('marketing',c.marketing?'allow':'deny'); }catch(e){} }
		document.querySelectorAll('script[type="text/plain"][data-fc-consent]').forEach(function(s){
			var cat=s.getAttribute('data-fc-consent'); if(!c[cat]||s.dataset.fcAtivado) return;
			var n=document.createElement('script'); for(var i=0;i<s.attributes.length;i++){ var a=s.attributes[i]; if(a.name!=='type'&&a.name!=='data-fc-consent') n.setAttribute(a.name,a.value); }
			n.text=s.text; s.dataset.fcAtivado='1'; s.parentNode.insertBefore(n,s.nextSibling);
		});
		window.fedcorpConsent={get:function(){return c;},abrir:abrir};
		document.dispatchEvent(new CustomEvent('fedcorp:consent',{detail:c}));
	}
	function registrar(c,origem){
		try{ var body=JSON.stringify({statistics:!!c.statistics,marketing:!!c.marketing,origem:origem,versao:c.v});
			if(navigator.sendBeacon){ navigator.sendBeacon(REST, new Blob([body],{type:'application/json'})); }
			else{ fetch(REST,{method:'POST',headers:{'Content-Type':'application/json'},body:body,keepalive:true}); } }catch(e){}
	}
	function decidir(c,origem){ c.v=1; c.ts=new Date().toISOString(); gravar(c); aplicar(c); registrar(c,origem); fechar(); }
	function abrir(){ var c=ler()||{}; stat.checked=!!c.statistics; mkt.checked=!!c.marketing; el.hidden=false; el.classList.add('is-aberto'); el.querySelector('[data-fc-acao="aceitar"]').focus(); }
	function fechar(){ el.classList.remove('is-aberto','is-prefs'); el.hidden=true; btnPrefs.setAttribute('aria-expanded','false'); btnSalvar.hidden=true; }
	el.addEventListener('click',function(e){
		var b=e.target.closest('[data-fc-acao]'); if(!b) return; var acao=b.getAttribute('data-fc-acao');
		if(acao==='aceitar') decidir({statistics:true,marketing:true},'aceitar_todos');
		else if(acao==='necessarios') decidir({statistics:false,marketing:false},'apenas_necessarios');
		else if(acao==='prefs'){ var ab=el.classList.toggle('is-prefs'); btnPrefs.setAttribute('aria-expanded',ab?'true':'false'); btnSalvar.hidden=!ab; if(ab) stat.focus(); }
		else if(acao==='salvar') decidir({statistics:stat.checked,marketing:mkt.checked},'preferencias');
	});
	el.addEventListener('keydown',function(e){ if(e.key==='Escape'&&ler()) fechar(); });
	document.addEventListener('click',function(e){ var a=e.target.closest('[data-fc-abrir-cookies]'); if(a){ e.preventDefault(); abrir(); } });
	var atual=ler();
	if(atual){ aplicar(atual); } else { abrir(); }
})();
</script>
	<?php
}

/* 3) Registro de consentimento (prova), sem dados pessoais diretos */
add_action( 'init', function () {
	register_post_type( 'fc_consentimento', array(
		'labels'       => array( 'name' => 'Consentimentos de cookies', 'singular_name' => 'Consentimento' ),
		'public'       => false,
		'show_ui'      => true,
		'show_in_menu' => 'tools.php',
		'supports'     => array( 'title' ),
		'capability_type' => 'post',
		'capabilities' => array( 'create_posts' => 'do_not_allow' ),
		'map_meta_cap' => true,
	) );
} );

add_action( 'rest_api_init', function () {
	register_rest_route( 'fedcorp/v1', '/consentimento', array(
		'methods'             => 'POST',
		'permission_callback' => '__return_true',
		'callback'            => function ( WP_REST_Request $r ) {
			$st = (bool) $r->get_param( 'statistics' );
			$mk = (bool) $r->get_param( 'marketing' );
			$origem = sanitize_key( (string) $r->get_param( 'origem' ) );
			$hash = fedcorp_lgpd_hash_ip();
			// Anti-abuso simples: no máximo 1 registro por minuto por hash.
			$chave = 'fc_cons_' . $hash;
			if ( get_transient( $chave ) ) {
				return new WP_REST_Response( array( 'ok' => true, 'dup' => true ), 200 );
			}
			set_transient( $chave, 1, MINUTE_IN_SECONDS );
			$ua = isset( $_SERVER['HTTP_USER_AGENT'] ) ? substr( sanitize_text_field( wp_unslash( $_SERVER['HTTP_USER_AGENT'] ) ), 0, 200 ) : '';
			$id = wp_insert_post( array(
				'post_type'   => 'fc_consentimento',
				'post_status' => 'private',
				'post_title'  => sprintf( '%s · est=%s mkt=%s · %s', current_time( 'Y-m-d H:i:s' ), $st ? 'sim' : 'não', $mk ? 'sim' : 'não', $origem ),
				'meta_input'  => array( 'statistics' => $st ? 1 : 0, 'marketing' => $mk ? 1 : 0, 'origem' => $origem, 'hash' => $hash, 'navegador' => $ua, 'politica_versao' => fedcorp_lgpd_dado( 'versao_termos' ) ),
			) );
			return new WP_REST_Response( array( 'ok' => (bool) $id ), 200 );
		},
	) );
} );

/* 4) Retenção: apaga registros de consentimento mais antigos que N anos */
add_action( 'init', function () {
	if ( ! wp_next_scheduled( 'fedcorp_lgpd_limpeza' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'fedcorp_lgpd_limpeza' );
	}
} );
add_action( 'fedcorp_lgpd_limpeza', function () {
	$anos = (int) fedcorp_lgpd_dado( 'retencao_consent' );
	$velhos = get_posts( array( 'post_type' => 'fc_consentimento', 'post_status' => 'any', 'posts_per_page' => 500, 'fields' => 'ids', 'date_query' => array( array( 'before' => $anos . ' years ago' ) ) ) );
	foreach ( $velhos as $id ) {
		wp_delete_post( $id, true );
	}
} );
