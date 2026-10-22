<?php
/**
 * Plugin Name: Fedcorp – Menu Lateral de Atalhos
 * Description: Barra lateral fixa com ícones (estilo "atalhos de serviços"). No celular vira uma barra inferior rolável.
 * Version:     1.3.0
 * Author:      Grupo Fedcorp
 *
 * Instalação: copiar este arquivo para wp-content/mu-plugins/ (criar a pasta se não existir).
 * Plugins em mu-plugins são carregados automaticamente — não precisa ativar no painel.
 * Para editar os itens, altere a função fedcorp_menu_lateral_itens() abaixo.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------
 * 1) CONFIGURAÇÃO — itens do menu (ordem = ordem na tela)
 *    'url'   : caminho no site (começando com /) ou link completo
 *    'label' : texto que aparece ao passar o mouse
 *    'icon'  : nome de um ícone definido em fedcorp_menu_lateral_icone()
 * ------------------------------------------------------------------ */
function fedcorp_menu_lateral_itens() {
	$itens = array(
		array( 'url' => '/',                                                     'label' => 'Início',                                   'icon' => 'home' ),
		array( 'url' => '/area-restrita/',                                       'label' => 'Área Restrita',                            'icon' => 'lock-user' ),

		// --- Plataformas (abrem em nova aba) — opções 3 a 7 ---
		array( 'url' => 'https://incendiofedcorp.com.br/login',                  'label' => 'Plataforma de Incêndio Locação',           'icon' => 'flame' ),
		array( 'url' => 'https://fedcorp.store/login',                           'label' => 'Plataforma Comercial',                     'icon' => 'store' ),
		array( 'url' => 'https://multicalculofedcorp.com.br/',                   'label' => 'Multicálculo de Condomínio',               'icon' => 'calculator' ),
		array( 'url' => 'https://plataforma.web.segimob.com/auth/login/E04BC0B1-109E-495F-893E-0F3AD5AF2D16', 'label' => 'Cálculo de Seguro Fiança', 'icon' => 'key-percent' ),
		array( 'url' => 'https://sinistro.grupofedcorp.com.br/#login',           'label' => 'Sinistros Fedcorp',                        'icon' => 'alert-doc' ),

		/* ============================================================
		 * BOTÕES RESERVA — para ativar um, apague as duas barras "//"
		 * do início da linha, coloque a linha onde quiser na lista acima
		 * (ou deixe aqui) e salve. 'url' vazia = botão "em breve".
		 * ============================================================ */
		// array( 'url' => '',                                                      'label' => 'Plataforma de Seguro Locação',             'icon' => 'house-shield' ),
		// array( 'divisor' => true ), // linha separadora
		// array( 'url' => '/segmentos/vistorias-locaticias-e-prediais/',           'label' => 'Vistorias Locatícias e Prediais',          'icon' => 'clipboard-house' ),
		// array( 'url' => '/segmentos/plataforma-de-seguros-e-assistencias-24h/',  'label' => 'Plataforma de Seguros e Assistência 24h',  'icon' => 'shield-24' ),
		// array( 'url' => '/segmentos/fiancas-locaticias/',                        'label' => 'Fianças Locatícias',                       'icon' => 'key-house' ),
		// array( 'url' => '/segmentos/medicina-e-seguranca-do-trabalho/',          'label' => 'Medicina e Segurança do Trabalho',         'icon' => 'helmet-heart' ),
		// array( 'url' => '/segmentos/analise-cadastral/',                         'label' => 'Análise Cadastral',                        'icon' => 'user-search' ),
		// array( 'url' => '/segmentos/beneficios/',                                'label' => 'Benefícios – Clube de Vantagens',          'icon' => 'gift' ),
		// array( 'url' => '/produtos/',                                            'label' => 'Produtos',                                 'icon' => 'grid' ),
		// array( 'url' => '/o-grupo/',                                             'label' => 'O Grupo',                                  'icon' => 'building' ),
		// array( 'url' => '/fale-conosco/',                                        'label' => 'Fale Conosco',                             'icon' => 'chat' ),
	);

	/** Permite alterar os itens por outro código: add_filter( 'fedcorp_menu_lateral_itens', ... ) */
	return apply_filters( 'fedcorp_menu_lateral_itens', $itens );
}

/* Lado da barra no computador: 'esquerda' ou 'direita' */
if ( ! defined( 'FEDCORP_MENU_LADO' ) ) {
	define( 'FEDCORP_MENU_LADO', 'esquerda' );
}

/* ------------------------------------------------------------------
 * 2) ÍCONES — SVG em linha (traço), herdam a cor via currentColor
 * ------------------------------------------------------------------ */
function fedcorp_menu_lateral_icone( $nome ) {
	$p = array(
		'home'            => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5.5 9.5V20h13V9.5"/><path d="M10 20v-5.5h4V20"/>',
		'lock-user'       => '<circle cx="9.5" cy="8" r="3.5"/><path d="M3.5 20c0-3.6 2.7-6 6-6 1.2 0 2.3.3 3.2.8"/><rect x="14.5" y="15" width="6.5" height="5.5" rx="1.2"/><path d="M16 15v-1.6a1.75 1.75 0 0 1 3.5 0V15"/>',
		'clipboard-house' => '<rect x="5" y="4.5" width="14" height="16.5" rx="2"/><path d="M9 4.5V3h6v1.5"/><path d="M8.5 13 12 10l3.5 3"/><path d="M9.5 12.2V17h5v-4.8"/>',
		'shield-24'       => '<path d="M12 3 4.5 6v5.5c0 4.6 3.2 8.2 7.5 9.5 4.3-1.3 7.5-4.9 7.5-9.5V6L12 3Z"/><path d="M8.3 10.2c.3-.8 1-1.2 1.8-1.2 1 0 1.7.7 1.7 1.6 0 1.4-3.5 2.6-3.5 4.4h3.5"/><path d="M15.2 9.2l-2 3.8h3.3"/><path d="M15.7 11.6V15"/>',
		'key-house'       => '<path d="M3 11 10 5l7 6"/><path d="M5 9.5V19h5"/><circle cx="16" cy="15.5" r="2.5"/><path d="M18.2 14.4 21 12"/><path d="M20 12.9l1 1"/>',
		'helmet-heart'    => '<path d="M4 14.5a8 8 0 0 1 16 0"/><path d="M3 14.5h18v2H3z"/><path d="M12 6.5v3"/><path d="M12 20.5s-2.8-1.6-2.8-3.3"/><path d="M8.5 10.5c.6-1.3 1.6-2.3 3-2.8"/>',
		'user-search'     => '<circle cx="9" cy="8" r="3.5"/><path d="M3 20c0-3.4 2.6-6 6-6 .9 0 1.7.2 2.4.5"/><circle cx="16.5" cy="15.5" r="3"/><path d="m18.7 17.7 2.3 2.3"/>',
		'gift'            => '<rect x="3.5" y="9" width="17" height="4" rx="1"/><path d="M5 13v7.5h14V13"/><path d="M12 9v11.5"/><path d="M12 9c-1.5-3.5-5.5-4-5.5-1.5C6.5 9 12 9 12 9Z"/><path d="M12 9c1.5-3.5 5.5-4 5.5-1.5C17.5 9 12 9 12 9Z"/>',
		'grid'            => '<rect x="4" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="4" width="6.5" height="6.5" rx="1.5"/><rect x="4" y="13.5" width="6.5" height="6.5" rx="1.5"/><rect x="13.5" y="13.5" width="6.5" height="6.5" rx="1.5"/>',
		'building'        => '<path d="M4 21V5.5L12 3v18"/><path d="M12 8.5h8V21"/><path d="M3 21h18"/><path d="M7.5 8h1.5M7.5 11.5h1.5M7.5 15h1.5M15 12h1.5M15 15.5h1.5"/>',
		'calculator'      => '<rect x="5" y="3" width="14" height="18" rx="2"/><rect x="7.5" y="5.5" width="9" height="3.5" rx=".6"/><path d="M8.5 12.5h.01M12 12.5h.01M15.5 12.5h.01M8.5 15.5h.01M12 15.5h.01M8.5 18.5h.01M12 18.5h.01"/><path d="M15.5 15.5v3"/>',
		'key-percent'     => '<circle cx="7.5" cy="8.5" r="3.5"/><path d="M10 11l7 7"/><path d="M14.5 15.5l1.5-1.5M16.5 17.5l1.5-1.5"/><path d="M15 4.5l4.5 4.5"/><circle cx="15.2" cy="5" r=".9"/><circle cx="19.2" cy="8.8" r=".9"/>',
		'house-shield'    => '<path d="M3 11 11 4.5l8 6.5"/><path d="M5 9.5V19h5"/><path d="M17 12.5l-4 1.5v2.8c0 2 1.7 3.6 4 4.2 2.3-.6 4-2.2 4-4.2V14l-4-1.5Z"/><path d="m15.4 16.8 1.1 1.1 2-2"/>',
		'flame'           => '<path d="M12 21c-3.6 0-6.5-2.6-6.5-6.2 0-2.6 1.5-4.4 2.9-6 .3 1.6 1.1 2.6 2.1 3.1C10.3 8.6 11.4 5.6 14 3c.2 2.9 1.6 4.6 2.8 6.2 1 1.4 1.7 3 1.7 5.6 0 3.6-2.9 6.2-6.5 6.2Z"/><path d="M12 21c-1.6 0-2.8-1.2-2.8-2.8 0-1.6 1.2-2.7 2.8-4.2 1.6 1.5 2.8 2.6 2.8 4.2 0 1.6-1.2 2.8-2.8 2.8Z"/>',
		'store'           => '<path d="M4 9.5 5.5 4h13L20 9.5"/><path d="M4 9.5c0 1.4 1.1 2.5 2.7 2.5s2.6-1.1 2.6-2.5c0 1.4 1.1 2.5 2.7 2.5s2.7-1.1 2.7-2.5c0 1.4 1 2.5 2.6 2.5S20 10.9 20 9.5"/><path d="M5.5 12v8.5h13V12"/><path d="M10 20.5v-5h4v5"/>',
		'alert-doc'       => '<path d="M13.5 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8.5L13.5 3Z"/><path d="M13.5 3v5.5H19"/><path d="M12 11.5v4"/><path d="M12 18h.01"/>',
		'chat'            => '<path d="M20.5 12c0 4.1-3.8 7.5-8.5 7.5-1.3 0-2.6-.3-3.7-.8L3.5 20l1.4-3.9A7 7 0 0 1 3.5 12C3.5 7.9 7.3 4.5 12 4.5s8.5 3.4 8.5 7.5Z"/><path d="M8.5 12h.01M12 12h.01M15.5 12h.01"/>',
	);
	$d = isset( $p[ $nome ] ) ? $p[ $nome ] : $p['home'];
	return '<svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $d . '</svg>';
}

/* ------------------------------------------------------------------
 * 3) RENDERIZAÇÃO
 * ------------------------------------------------------------------ */
function fedcorp_menu_lateral_deve_exibir() {
	if ( is_admin() || wp_doing_ajax() || is_feed() || is_embed() ) {
		return false;
	}
	// Não exibir dentro do editor do Elementor (a pré-visualização pública continua mostrando)
	if ( isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return false;
	}
	return (bool) apply_filters( 'fedcorp_menu_lateral_exibir', true );
}

function fedcorp_menu_lateral_url_atual() {
	$uri = isset( $_SERVER['REQUEST_URI'] ) ? wp_unslash( $_SERVER['REQUEST_URI'] ) : '/'; // phpcs:ignore
	$path = wp_parse_url( $uri, PHP_URL_PATH );
	return trailingslashit( $path ? $path : '/' );
}

add_action( 'wp_footer', 'fedcorp_menu_lateral_render', 20 );
function fedcorp_menu_lateral_render() {
	if ( ! fedcorp_menu_lateral_deve_exibir() ) {
		return;
	}
	$atual = fedcorp_menu_lateral_url_atual();
	$lado  = ( 'direita' === FEDCORP_MENU_LADO ) ? 'fc-rail--direita' : 'fc-rail--esquerda';

	$itens = fedcorp_menu_lateral_itens();
	$total = count( array_filter( $itens, function ( $i ) { return empty( $i['divisor'] ); } ) );

	echo '<nav class="fc-rail ' . esc_attr( $lado ) . '" aria-label="Atalhos de serviços" style="--fc-n:' . (int) $total . '">';
	echo '<ul class="fc-rail__lista">';
	foreach ( $itens as $item ) {
		if ( ! empty( $item['divisor'] ) ) {
			echo '<li class="fc-rail__divisor" role="separator" aria-hidden="true"></li>';
			continue;
		}
		// Item ainda sem link: botão desativado com aviso "em breve"
		if ( '' === trim( (string) $item['url'] ) ) {
			printf(
				'<li class="fc-rail__item is-em-breve"><span class="fc-rail__link" role="link" aria-disabled="true" tabindex="0" aria-label="%1$s (em breve)">%2$s<span class="fc-rail__label" aria-hidden="true">%3$s <em>em breve</em></span></span></li>',
				esc_attr( $item['label'] ),
				fedcorp_menu_lateral_icone( $item['icon'] ),
				esc_html( $item['label'] )
			);
			continue;
		}
		$externo = (bool) preg_match( '#^https?://#i', $item['url'] );
		$href    = $externo ? $item['url'] : home_url( $item['url'] );
		$path    = $externo ? '' : trailingslashit( wp_parse_url( $href, PHP_URL_PATH ) ?: '/' );
		$ativo   = ( ! $externo && $path === $atual );
		printf(
			'<li class="fc-rail__item%1$s"><a class="fc-rail__link" href="%2$s"%3$s%4$s aria-label="%5$s">%6$s<span class="fc-rail__label" aria-hidden="true">%7$s</span></a></li>',
			( $ativo ? ' is-ativo' : '' ) . ( $externo ? ' is-externo' : '' ),
			esc_url( $href ),
			$ativo ? ' aria-current="page"' : '',
			$externo ? ' target="_blank" rel="noopener"' : '',
			esc_attr( $item['label'] . ( $externo ? ' (abre em nova aba)' : '' ) ),
			fedcorp_menu_lateral_icone( $item['icon'] ), // SVG fixo definido acima
			esc_html( $item['label'] )
		);
	}
	echo '</ul></nav>';
}

/* ------------------------------------------------------------------
 * 4) ESTILO — cores do Kit do Elementor do site
 *    Azul primário #004077 · Amarelo secundário #FFC203 · Fonte Poppins
 * ------------------------------------------------------------------ */
add_action( 'wp_head', 'fedcorp_menu_lateral_css', 50 );
function fedcorp_menu_lateral_css() {
	if ( ! fedcorp_menu_lateral_deve_exibir() ) {
		return;
	}
	?>
<style id="fedcorp-menu-lateral">
.fc-rail{
	--fc-azul:#004077; --fc-azul-escuro:#002d54; --fc-amarelo:#FFC203; --fc-branco:#fff;
	--fc-n:14; --fc-gap:6px;
	/* Tamanho do botão se ajusta à altura da tela para todos os itens caberem (mín. 28px, máx. 46px) */
	--fc-tam:clamp(28px, calc((100vh - 150px - (var(--fc-n) * var(--fc-gap))) / var(--fc-n)), 46px);
	position:fixed; top:50%; transform:translateY(-50%); z-index:998;
	font-family:Poppins, system-ui, -apple-system, "Segoe UI", sans-serif;
}
.fc-rail--esquerda{ left:14px; }
.fc-rail--direita{ right:14px; }
.fc-rail__lista{
	list-style:none; margin:0; padding:10px 8px;
	display:flex; flex-direction:column; gap:var(--fc-gap);
	background:rgba(0,64,119,.92);
	-webkit-backdrop-filter:blur(6px); backdrop-filter:blur(6px);
	border-radius:22px;
	box-shadow:0 10px 30px rgba(0,30,60,.28);
}
.fc-rail__item{ position:relative; margin:0; padding:0; }
.fc-rail__link{
	position:relative; display:flex; align-items:center; justify-content:center; box-sizing:border-box;
	width:var(--fc-tam); height:var(--fc-tam);
	border-radius:14px; border:1px solid rgba(255,255,255,.28);
	color:var(--fc-branco); text-decoration:none;
	transition:background-color .2s ease, border-color .2s ease, color .2s ease, transform .2s ease;
}
.fc-rail__link:hover, .fc-rail__link:focus-visible{
	background:rgba(255,255,255,.14); border-color:rgba(255,255,255,.6); color:var(--fc-branco);
	transform:scale(1.06); outline:none;
}
.fc-rail__link:focus-visible{ box-shadow:0 0 0 3px var(--fc-amarelo); }
.fc-rail__item.is-ativo .fc-rail__link{
	background:var(--fc-amarelo); border-color:var(--fc-amarelo); color:var(--fc-azul-escuro);
	box-shadow:0 4px 12px rgba(255,194,3,.45);
}
/* Rótulo que desliza ao passar o mouse */
.fc-rail__label{
	position:absolute; top:50%; white-space:nowrap; pointer-events:none;
	padding:8px 14px; border-radius:10px;
	background:var(--fc-azul); color:var(--fc-branco);
	font-size:14px; font-weight:500; line-height:1.2;
	box-shadow:0 6px 18px rgba(0,30,60,.25);
	opacity:0; visibility:hidden;
	transition:opacity .18s ease, transform .18s ease, visibility .18s;
}
.fc-rail--esquerda .fc-rail__label{ left:calc(100% + 14px); transform:translate(-6px,-50%); }
.fc-rail--direita  .fc-rail__label{ right:calc(100% + 14px); transform:translate(6px,-50%); }
.fc-rail__label::before{
	content:""; position:absolute; top:50%; width:10px; height:10px; background:inherit;
	transform:translateY(-50%) rotate(45deg);
}
.fc-rail--esquerda .fc-rail__label::before{ left:-4px; }
.fc-rail--direita  .fc-rail__label::before{ right:-4px; }
.fc-rail__link:hover .fc-rail__label,
.fc-rail__link:focus-visible .fc-rail__label{ opacity:1; visibility:visible; transform:translate(0,-50%); }

/* Plataformas externas: seta "↗" no rótulo */
.fc-rail__item.is-externo .fc-rail__label::after{ content:" ↗"; color:var(--fc-amarelo); font-weight:600; }
/* Item ainda sem link */
.fc-rail__item.is-em-breve .fc-rail__link{ cursor:not-allowed; border-style:dashed; border-color:rgba(255,255,255,.3); }
.fc-rail__item.is-em-breve .fc-rail__link svg{ opacity:.45; }
.fc-rail__item.is-em-breve .fc-rail__link:hover,
.fc-rail__item.is-em-breve .fc-rail__link:focus-visible{ transform:none; background:rgba(255,255,255,.06); }
.fc-rail__label em{ font-style:normal; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em;
	margin-left:6px; padding:2px 6px; border-radius:6px; background:var(--fc-amarelo); color:var(--fc-azul-escuro); }

.fc-rail__link svg{ width:calc(var(--fc-tam) * .48); height:calc(var(--fc-tam) * .48); }
.fc-rail__link{ border-radius:calc(var(--fc-tam) * .3); }
/* Linha divisória entre plataformas e páginas do site */
.fc-rail__divisor{ height:1px; margin:2px 6px; background:rgba(255,255,255,.35); list-style:none; }
@media (max-height:820px){ .fc-rail{ --fc-gap:4px; } .fc-rail__lista{ padding:7px 6px; } }

/* Celular: barra inferior rolável */
@media (max-width:767px){
	.fc-rail, .fc-rail--esquerda, .fc-rail--direita{
		top:auto; bottom:0; left:0; right:0; transform:none;
	}
	.fc-rail__lista{
		flex-direction:row; gap:6px; padding:8px 10px calc(8px + env(safe-area-inset-bottom));
		border-radius:18px 18px 0 0; overflow-x:auto; scroll-snap-type:x proximity;
		scrollbar-width:none;
	}
	.fc-rail__lista::-webkit-scrollbar{ display:none; }
	.fc-rail__item{ flex:0 0 auto; scroll-snap-align:start; }
	.fc-rail{ --fc-tam:44px; }
	.fc-rail__label{ display:none; }
	.fc-rail__divisor{ flex:0 0 1px; width:1px; height:auto; margin:6px 2px; }
	body{ padding-bottom:72px; }
	/* Botão do WhatsApp (Joinchat) acima da barra */
	.joinchat{ --bottom:84px; bottom:84px; }
}
@media (prefers-reduced-motion:reduce){
	.fc-rail__link, .fc-rail__label{ transition:none; }
	.fc-rail__link:hover, .fc-rail__link:focus-visible{ transform:none; }
}
@media print{ .fc-rail{ display:none !important; } }
</style>
	<?php
}
