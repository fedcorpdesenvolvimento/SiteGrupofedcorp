<?php
/**
 * Barra legal no rodapé: links para as políticas, "Gerenciar cookies" e aviso de direitos autorais.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_footer', function () {
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore
		return;
	}
	$links = array(
		'politica-de-privacidade' => 'Política de Privacidade',
		'politica-de-cookies'     => 'Política de Cookies',
		'termos-de-uso'           => 'Termos de Uso',
		'direitos-do-titular'     => 'Direitos do Titular (LGPD)',
		'acessibilidade'          => 'Acessibilidade',
		'direitos-autorais'       => 'Direitos Autorais',
	);
	?>
<style id="fedcorp-rodape-legal">
.fc-legalbar{ background:#002d54; color:#cfe0f0; font-family:Poppins, system-ui, sans-serif; font-size:13px; padding:16px 20px; }
.fc-legalbar__in{ max-width:1200px; margin:0 auto; display:flex; flex-wrap:wrap; gap:10px 22px; align-items:center; justify-content:space-between; }
.fc-legalbar nav{ display:flex; flex-wrap:wrap; gap:6px 18px; }
.fc-legalbar a, .fc-legalbar button{ color:#fff; text-decoration:none; background:none; border:0; padding:0; font:inherit; cursor:pointer; }
.fc-legalbar a:hover, .fc-legalbar a:focus-visible, .fc-legalbar button:hover, .fc-legalbar button:focus-visible{ color:#FFC203; text-decoration:underline; outline:none; }
.fc-legalbar a:focus-visible, .fc-legalbar button:focus-visible{ outline:3px solid #FFC203; outline-offset:3px; border-radius:4px; }
.fc-legalbar__copy{ margin:0; }
@media (max-width:767px){ .fc-legalbar{ padding-bottom:88px; } }
</style>
<div class="fc-legalbar" role="region" aria-label="Informações legais">
	<div class="fc-legalbar__in">
		<nav aria-label="Políticas e documentos legais">
			<?php foreach ( $links as $slug => $t ) : ?>
				<a href="<?php echo esc_url( fedcorp_lgpd_url( $slug ) ); ?>"><?php echo esc_html( $t ); ?></a>
			<?php endforeach; ?>
			<button type="button" data-fc-abrir-cookies>Gerenciar cookies</button>
		</nav>
		<p class="fc-legalbar__copy">© <span data-fc-ano><?php echo esc_html( gmdate( 'Y' ) ); ?></span> <?php echo esc_html( fedcorp_lgpd_dado( 'razao_social' ) ); ?>. Todos os direitos reservados.<?php if ( false === strpos( fedcorp_lgpd_dado( 'cnpj' ), 'PREENCHER' ) ) : ?> CNPJ <?php echo esc_html( fedcorp_lgpd_dado( 'cnpj' ) ); ?>.<?php endif; ?></p>
	</div>
</div>
<script>document.querySelectorAll('[data-fc-ano]').forEach(function(e){e.textContent=new Date().getFullYear();});</script>
	<?php
}, 35 );
