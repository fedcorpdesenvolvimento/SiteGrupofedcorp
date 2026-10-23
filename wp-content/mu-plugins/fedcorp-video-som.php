<?php
/**
 * Plugin Name: Fedcorp – Botão de som no vídeo da Home
 * Description: Vídeos do Elementor que iniciam sozinhos (mudos) ganham um botão no canto inferior direito para ativar/desativar o som.
 * Version:     1.0.0
 * Author:      Grupo Fedcorp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_footer', 'fedcorp_video_som_assets', 30 );
function fedcorp_video_som_assets() {
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	?>
<style id="fedcorp-video-som">
.elementor-widget-video .elementor-wrapper.fc-som{ position:relative; }
.fc-som__btn{
	position:absolute; right:14px; bottom:14px; z-index:5;
	width:46px; height:46px; border-radius:50%; border:0; cursor:pointer; padding:0;
	display:flex; align-items:center; justify-content:center;
	background:rgba(0,64,119,.85); color:#fff;
	box-shadow:0 4px 14px rgba(0,30,60,.35);
	transition:background-color .2s ease, transform .2s ease;
	-webkit-backdrop-filter:blur(4px); backdrop-filter:blur(4px);
}
.fc-som__btn:hover, .fc-som__btn:focus-visible{ background:#FFC203; color:#002d54; transform:scale(1.08); outline:none; }
.fc-som__btn svg{ width:24px; height:24px; fill:none; stroke:currentColor; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.fc-som__btn .fc-som__on, .fc-som--ligado .fc-som__btn .fc-som__off{ display:none; }
.fc-som--ligado .fc-som__btn .fc-som__on{ display:block; }
.fc-som__btn .fc-som__dica{
	position:absolute; right:calc(100% + 10px); top:50%; transform:translateY(-50%);
	white-space:nowrap; padding:6px 12px; border-radius:8px; background:#004077; color:#fff;
	font:500 13px/1.2 Poppins, system-ui, sans-serif; pointer-events:none;
	opacity:0; transition:opacity .2s ease;
}
.fc-som__btn:hover .fc-som__dica{ opacity:1; }
.fc-som--ligado .fc-som__btn .fc-som__dica{ display:none; }
@media (max-width:767px){ .fc-som__btn{ width:40px; height:40px; right:10px; bottom:10px; } .fc-som__btn .fc-som__dica{ display:none; } }
</style>
<script id="fedcorp-video-som-js">
(function(){
	var ICON_OFF = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="m22 9-6 6"/><path d="m16 9 6 6"/></svg>';
	var ICON_ON  = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M11 5 6 9H3v6h3l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M18.5 5.5a9 9 0 0 1 0 13"/></svg>';
	function montar(video){
		var wrap = video.closest('.elementor-wrapper'); if (!wrap || wrap.querySelector('.fc-som__btn')) return;
		wrap.classList.add('fc-som');
		var btn = document.createElement('button');
		btn.type = 'button'; btn.className = 'fc-som__btn'; btn.setAttribute('aria-label', 'Ativar som do vídeo'); btn.setAttribute('aria-pressed', 'false');
		btn.innerHTML = '<span class="fc-som__off">' + ICON_OFF + '</span><span class="fc-som__on">' + ICON_ON + '</span><span class="fc-som__dica">Ativar som</span>';
		btn.addEventListener('click', function(e){
			e.preventDefault(); e.stopPropagation();
			video.muted = !video.muted;
			if (!video.muted) { video.volume = 1; if (video.paused) { video.play(); } }
			wrap.classList.toggle('fc-som--ligado', !video.muted);
			btn.setAttribute('aria-pressed', video.muted ? 'false' : 'true');
			btn.setAttribute('aria-label', video.muted ? 'Ativar som do vídeo' : 'Desativar som do vídeo');
		});
		wrap.appendChild(btn);
	}
	function iniciar(){
		document.querySelectorAll('.elementor-widget-video video[autoplay][muted]').forEach(montar);
	}
	if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', iniciar); } else { iniciar(); }
	window.addEventListener('load', iniciar);
})();
</script>
	<?php
}
