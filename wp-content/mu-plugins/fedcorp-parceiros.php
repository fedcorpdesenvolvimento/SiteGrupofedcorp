<?php
/**
 * Plugin Name: Fedcorp – Seguradoras Parceiras
 * Description: Seção de seguradoras parceiras (carrossel de logos coloridos). Use o shortcode [fedcorp_parceiros] em qualquer página.
 * Version:     1.1.0
 * Author:      Grupo Fedcorp
 *
 * Instalação:
 *   1) Copie este arquivo para wp-content/mu-plugins/
 *   2) Coloque os logos em wp-content/mu-plugins/fedcorp-parceiros/logos/
 *      com o nome indicado em 'arquivo' abaixo + extensão (.svg, .webp ou .png).
 *      Ex.: porto-seguro.svg, allianz.png
 *   3) No Elementor, adicione um widget "Shortcode" com: [fedcorp_parceiros]
 *
 * Enquanto o logo de uma seguradora não estiver na pasta, aparece o NOME dela num cartão.
 *
 * Opções do shortcode:
 *   [fedcorp_parceiros titulo="Seguradoras parceiras" subtitulo="..." modo="carrossel"]
 *   modo="carrossel" (padrão, rola sozinho)  ou  modo="grade" (todos os logos parados, em grade)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* ------------------------------------------------------------------
 * 1) LISTA DE SEGURADORAS (ordem = ordem na tela)
 *    'nome'    : nome exibido (texto alternativo do logo)
 *    'arquivo' : nome do arquivo do logo, SEM extensão
 *    'site'    : opcional — link ao clicar no logo (deixe '' para não ter link)
 * ------------------------------------------------------------------ */
function fedcorp_parceiros_lista() {
	$lista = array(
		array( 'nome' => 'Porto Seguro',          'arquivo' => 'porto-seguro',     'site' => '' ),
		array( 'nome' => 'Allianz Brasil',        'arquivo' => 'allianz',          'site' => '' ),
		array( 'nome' => 'Bradesco Seguros',      'arquivo' => 'bradesco-seguros', 'site' => '' ),
		array( 'nome' => 'Pottencial Seguradora', 'arquivo' => 'pottencial',       'site' => '' ),
		array( 'nome' => 'HDI Seguros',           'arquivo' => 'hdi',              'site' => '' ),
		array( 'nome' => 'Chubb Seguros',         'arquivo' => 'chubb',            'site' => '' ),
		array( 'nome' => 'AXA Seguros',           'arquivo' => 'axa',              'site' => '' ),
		array( 'nome' => 'Brasilcap',             'arquivo' => 'brasilcap',        'site' => '' ),
		array( 'nome' => 'Tokio Marine',          'arquivo' => 'tokio-marine',     'site' => '' ),
		array( 'nome' => 'Too Seguros',           'arquivo' => 'too-seguros',      'site' => '' ),
		array( 'nome' => 'Suhai Seguradora',      'arquivo' => 'suhai',            'site' => '' ),
		array( 'nome' => 'Amil',                  'arquivo' => 'amil',             'site' => '' ),
		array( 'nome' => 'Yelum Seguros',         'arquivo' => 'yelum',            'site' => '' ),
		array( 'nome' => 'Azul Seguros',          'arquivo' => 'azul-seguros',     'site' => '' ),
		array( 'nome' => 'Mapfre Seguros',        'arquivo' => 'mapfre',           'site' => '' ),
	);
	return apply_filters( 'fedcorp_parceiros_lista', $lista );
}

/* ------------------------------------------------------------------
 * 2) Localiza o logo na pasta (svg > webp > png > jpg)
 * ------------------------------------------------------------------ */
function fedcorp_parceiros_logo_url( $arquivo ) {
	$dir = WPMU_PLUGIN_DIR . '/fedcorp-parceiros/logos/';
	$url = WPMU_PLUGIN_URL . '/fedcorp-parceiros/logos/';
	foreach ( array( 'svg', 'webp', 'png', 'jpg', 'jpeg' ) as $ext ) {
		$caminho = $dir . $arquivo . '.' . $ext;
		if ( file_exists( $caminho ) ) {
			return $url . rawurlencode( $arquivo . '.' . $ext ) . '?v=' . filemtime( $caminho );
		}
	}
	return '';
}

function fedcorp_parceiros_card( $p, $oculto = false ) {
	$logo  = fedcorp_parceiros_logo_url( $p['arquivo'] );
	$miolo = $logo
		? sprintf( '<img src="%1$s" alt="%2$s" loading="lazy" decoding="async">', esc_url( $logo ), esc_attr( $oculto ? '' : $p['nome'] ) )
		: sprintf( '<span class="fc-parc__nome">%s</span>', esc_html( $p['nome'] ) );

	if ( ! empty( $p['site'] ) ) {
		$miolo = sprintf(
			'<a href="%1$s" target="_blank" rel="noopener"%2$s>%3$s</a>',
			esc_url( $p['site'] ),
			$oculto ? ' tabindex="-1"' : '',
			$miolo
		);
	}
	return '<li class="fc-parc__card' . ( $logo ? '' : ' is-sem-logo' ) . '">' . $miolo . '</li>';
}

/* ------------------------------------------------------------------
 * 3) SHORTCODE [fedcorp_parceiros]
 * ------------------------------------------------------------------ */
add_shortcode( 'fedcorp_parceiros', 'fedcorp_parceiros_shortcode' );
function fedcorp_parceiros_shortcode( $atts ) {
	$a = shortcode_atts(
		array(
			'titulo'    => 'Seguradoras parceiras',
			'subtitulo' => 'Trabalhamos com as principais seguradoras do país para oferecer a melhor solução para sua administradora e seus condomínios.',
			'modo'      => 'carrossel',
		),
		$atts,
		'fedcorp_parceiros'
	);

	$lista = fedcorp_parceiros_lista();
	if ( empty( $lista ) ) {
		return '';
	}
	$carrossel = ( 'grade' !== $a['modo'] );

	$cards = '';
	foreach ( $lista as $p ) {
		$cards .= fedcorp_parceiros_card( $p );
	}
	// Segunda cópia (só visual) para o carrossel rodar sem emenda
	$copia = '';
	if ( $carrossel ) {
		foreach ( $lista as $p ) {
			$copia .= fedcorp_parceiros_card( $p, true );
		}
	}

	ob_start();
	?>
	<section class="fc-parc <?php echo $carrossel ? 'fc-parc--carrossel' : 'fc-parc--grade'; ?>" aria-labelledby="fc-parc-titulo" style="--fc-parc-n:<?php echo (int) count( $lista ); ?>">
		<div class="fc-parc__topo">
			<span class="fc-parc__selo">Parceiros</span>
			<h2 id="fc-parc-titulo" class="fc-parc__titulo"><?php echo esc_html( $a['titulo'] ); ?></h2>
			<?php if ( '' !== $a['subtitulo'] ) : ?>
				<p class="fc-parc__sub"><?php echo esc_html( $a['subtitulo'] ); ?></p>
			<?php endif; ?>
		</div>
		<div class="fc-parc__janela">
			<div class="fc-parc__trilho">
				<ul class="fc-parc__lista"><?php echo $cards; // phpcs:ignore -- montado e escapado acima ?></ul>
				<?php if ( $carrossel ) : ?>
					<ul class="fc-parc__lista" aria-hidden="true"><?php echo $copia; // phpcs:ignore ?></ul>
				<?php endif; ?>
			</div>
		</div>
	</section>
	<?php
	fedcorp_parceiros_css_once();
	return ob_get_clean();
}

/* ------------------------------------------------------------------
 * 4) ESTILO (cores do Kit: azul #004077, amarelo #FFC203, Poppins)
 * ------------------------------------------------------------------ */
function fedcorp_parceiros_css_once() {
	static $feito = false;
	if ( $feito ) {
		return;
	}
	$feito = true;
	?>
<style id="fedcorp-parceiros">
.fc-parc{ --fc-azul:#004077; --fc-amarelo:#FFC203; --fc-texto:#292929; --fc-card-w:190px; --fc-card-h:96px; --fc-gap:20px;
	font-family:Poppins, system-ui, -apple-system, "Segoe UI", sans-serif; padding:64px 0; text-align:center; }
.fc-parc__topo{ max-width:760px; margin:0 auto 36px; padding:0 20px; }
.fc-parc__selo{ display:inline-block; padding:4px 14px; border-radius:999px; background:rgba(255,194,3,.18); color:var(--fc-azul);
	font-size:13px; font-weight:600; letter-spacing:.06em; text-transform:uppercase; }
.fc-parc__titulo{ margin:12px 0 8px; color:var(--fc-azul); font-size:clamp(26px, 3vw, 36px); font-weight:700; line-height:1.2; }
.fc-parc__sub{ margin:0; color:var(--fc-texto); opacity:.8; font-size:16px; line-height:1.6; }

.fc-parc__lista{ list-style:none; margin:0; padding:0; display:flex; gap:var(--fc-gap); }
.fc-parc__card{ flex:0 0 var(--fc-card-w); height:var(--fc-card-h); margin:0; display:flex; align-items:center; justify-content:center;
	padding:16px 22px; box-sizing:border-box; background:#fff; border:1px solid #e6ebf1; border-radius:16px;
	box-shadow:0 4px 14px rgba(0,40,80,.06); transition:transform .25s ease, box-shadow .25s ease, border-color .25s ease; }
.fc-parc__card > a{ display:flex; align-items:center; justify-content:center; width:100%; height:100%; }
.fc-parc__card img{ max-width:100%; max-height:100%; width:auto; height:auto; object-fit:contain;
	transition:transform .25s ease; }
.fc-parc__card:hover{ transform:translateY(-3px); border-color:rgba(0,64,119,.25); box-shadow:0 10px 24px rgba(0,40,80,.12); }
.fc-parc__card:hover img{ transform:scale(1.05); }
.fc-parc__nome{ color:var(--fc-azul); font-weight:600; font-size:15px; line-height:1.25; }
.fc-parc__card.is-sem-logo{ border-style:dashed; }

/* Carrossel contínuo */
.fc-parc--carrossel .fc-parc__janela{ overflow:hidden; padding:8px 0 14px;
	-webkit-mask-image:linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent);
	        mask-image:linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); }
.fc-parc--carrossel .fc-parc__trilho{ display:flex; gap:var(--fc-gap); width:max-content;
	animation:fc-parc-rolar calc(var(--fc-parc-n) * 3s) linear infinite; }
.fc-parc--carrossel .fc-parc__janela:hover .fc-parc__trilho,
.fc-parc--carrossel .fc-parc__janela:focus-within .fc-parc__trilho{ animation-play-state:paused; }
@keyframes fc-parc-rolar{ to{ transform:translateX(calc(-50% - var(--fc-gap) / 2)); } }

/* Grade */
.fc-parc--grade .fc-parc__janela{ max-width:1140px; margin:0 auto; padding:0 20px; }
.fc-parc--grade .fc-parc__lista{ flex-wrap:wrap; justify-content:center; }

@media (max-width:767px){
	.fc-parc{ --fc-card-w:150px; --fc-card-h:80px; --fc-gap:14px; padding:44px 0; }
}
/* Quem pede menos movimento vê a grade parada */
@media (prefers-reduced-motion:reduce){
	.fc-parc--carrossel .fc-parc__trilho{ animation:none; width:auto; }
	.fc-parc--carrossel .fc-parc__lista[aria-hidden="true"]{ display:none; }
	.fc-parc--carrossel .fc-parc__lista{ flex-wrap:wrap; justify-content:center; }
	.fc-parc--carrossel .fc-parc__janela{ -webkit-mask-image:none; mask-image:none; padding:0 20px; }
}
</style>
	<?php
}
