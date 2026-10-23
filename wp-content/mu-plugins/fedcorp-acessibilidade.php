<?php
/**
 * Plugin Name: Fedcorp – Acessibilidade (WCAG 2.1 AA)
 * Description: Correções de contraste, foco visível para navegação por teclado, marcos de página (banner/main/contentinfo), destino do "pular para o conteúdo", nomes acessíveis em links de imagem e carrosséis operáveis por teclado.
 * Version:     1.0.0
 * Author:      Grupo Fedcorp
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', function () {
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore
		return;
	}
	?>
<style id="fedcorp-a11y">
/* 1) Contraste: azul de realce do Kit (#308FCE = 3,5:1 com branco) -> #1F6FA6 (5,4:1) */
.elementor-kit-10, :root{ --e-global-color-accent:#1F6FA6; }
.elementor-kit-10 .elementor-button, .elementor-kit-10 a{ --fc-a11y:1; }
/* Botão do WhatsApp da página Produtos: verde #25D366 com branco = 2:1 -> #0F7A6D (5,2:1) */
.fp-btn--wa{ background:#0F7A6D !important; color:#fff !important; }
.fp-btn--wa:hover, .fp-btn--wa:focus-visible{ background:#075E54 !important; }
.fp-eyebrow{ color:#1F6FA6 !important; }
.fp-legal__note{ color:#5f6c7b !important; }
/* Texto cinza claro sobre branco */
.elementor-widget-text-editor, .elementor-widget-heading .elementor-heading-title{ -webkit-font-smoothing:antialiased; }

/* 2) Foco visível (teclado) em tudo que é interativo */
a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible,
[tabindex]:not([tabindex="-1"]):focus-visible, .elementor-button:focus-visible, .jet-tabs__control:focus-visible,
.slick-arrow:focus-visible, .swiper-button-next:focus-visible, .swiper-button-prev:focus-visible, .jet-menu-item > a:focus-visible,
.jet-mobile-menu__toggle:focus-visible, .joinchat__button:focus-visible{
	outline:3px solid #FFC203 !important; outline-offset:3px !important; box-shadow:0 0 0 6px rgba(0,64,119,.35) !important; border-radius:6px;
}
a:focus:not(:focus-visible){ outline:none; }
/* Botão de fechar dos popups e setas de carrossel precisam ser visíveis ao foco */
.jet-popup__close-button:focus-visible{ outline:3px solid #FFC203 !important; }

/* 3) Pular para o conteúdo (tema Hello) – visível ao receber foco */
.skip-link.screen-reader-text:focus{
	position:fixed !important; top:12px; left:12px; z-index:100000; width:auto; height:auto; clip:auto; clip-path:none; overflow:visible;
	padding:12px 18px; background:#FFC203; color:#002d54; font:600 15px Poppins, system-ui, sans-serif; border-radius:10px; text-decoration:none;
	box-shadow:0 6px 20px rgba(0,30,60,.3); outline:3px solid #004077; outline-offset:2px;
}
#content:focus{ outline:none; }

/* 4) Texto legível: tamanho mínimo e respeito ao zoom do usuário */
html{ -webkit-text-size-adjust:100%; }
.elementor-widget-text-editor p{ font-size:max(1em, 15px); }

/* 5) Menos movimento para quem pede */
@media (prefers-reduced-motion:reduce){
	*, *::before, *::after{ animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; scroll-behavior:auto !important; }
	.elementor-widget-video video[autoplay]{ display:block; }
}

/* 6) Alvos de toque com no mínimo 44px nos menus e botões principais */
.fc-rail__link, .jet-popup__close-button, .joinchat__button{ min-width:44px; min-height:44px; }
</style>
	<?php
}, 70 );

add_action( 'wp_footer', function () {
	if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore
		return;
	}
	?>
<script id="fedcorp-a11y-js">
(function(){
	function q(s,c){ return Array.prototype.slice.call((c||document).querySelectorAll(s)); }
	function marcos(){
		var header=document.querySelector('.elementor-location-header'); if(header&&!header.closest('header')){ header.setAttribute('role','banner'); }
		var footer=document.querySelector('.elementor-location-footer'); if(footer&&!footer.closest('footer')){ footer.setAttribute('role','contentinfo'); }
		var main=document.querySelector('main, [role="main"]');
		if(!main){
			main=document.querySelector('[data-elementor-type="wp-page"], [data-elementor-type="wp-post"], .site-main, #content, .page-content');
			if(main){ main.setAttribute('role','main'); }
		}
		if(main){ if(!main.id) main.id='content'; if(!main.hasAttribute('tabindex')) main.setAttribute('tabindex','-1'); if(!main.getAttribute('aria-label')) main.setAttribute('aria-label','Conteúdo principal'); }
		q('.skip-link').forEach(function(a){ a.setAttribute('href','#'+(main?main.id:'content')); a.textContent='Pular para o conteúdo'; a.addEventListener('click',function(){ var m=document.getElementById(a.getAttribute('href').slice(1)); if(m){ setTimeout(function(){ m.focus(); },0); } }); });
	}
	function nomesDeLinks(){
		q('a').forEach(function(a){
			if((a.textContent||'').trim()||a.getAttribute('aria-label')||a.getAttribute('title')) return;
			var img=a.querySelector('img,svg');
			if(!img) return;
			var alt=img.getAttribute('alt')||img.getAttribute('aria-label')||'';
			if(!alt){
				var h=a.getAttribute('href')||''; var home=location.origin+'/';
				alt=(h===home||h===home.slice(0,-1)||h==='/'||/^https?:\/\/[^/]+\/?$/.test(h)) ? 'Grupo Fedcorp – página inicial' : (h.indexOf('wa.me')>-1?'Conversar pelo WhatsApp':'');
			}
			if(alt) a.setAttribute('aria-label',alt);
		});
		q('.elementor-widget-image img[alt=""], .jet-listing img[alt=""]').forEach(function(i){ i.setAttribute('role','presentation'); });
	}
	function carrosseis(){
		q('.slick-arrow, .swiper-button-next, .swiper-button-prev, .jet-arrow').forEach(function(b){
			if(!b.getAttribute('aria-label')){ b.setAttribute('aria-label', /next|right/i.test(b.className)?'Próximo':'Anterior'); }
			if(b.tagName!=='BUTTON'&&!b.hasAttribute('tabindex')){ b.setAttribute('tabindex','0'); b.setAttribute('role','button'); b.addEventListener('keydown',function(e){ if(e.key==='Enter'||e.key===' '){ e.preventDefault(); b.click(); } }); }
		});
		q('.jet-carousel, .swiper, .slick-slider').forEach(function(c){
			if(!c.getAttribute('role')){ c.setAttribute('role','region'); c.setAttribute('aria-roledescription','carrossel'); if(!c.getAttribute('aria-label')) c.setAttribute('aria-label','Carrossel de conteúdo'); }
			c.addEventListener('focusin',function(){ if(c.swiper&&c.swiper.autoplay){ c.swiper.autoplay.stop(); } if(window.jQuery&&jQuery(c).hasClass('slick-initialized')){ jQuery(c).slick('slickPause'); } });
		});
		q('.jet-tabs__control').forEach(function(t,i,all){
			t.addEventListener('keydown',function(e){
				var k=e.key, n=null;
				if(k==='ArrowRight'||k==='ArrowDown'){ n=all[(i+1)%all.length]; } else if(k==='ArrowLeft'||k==='ArrowUp'){ n=all[(i-1+all.length)%all.length]; } else if(k==='Home'){ n=all[0]; } else if(k==='End'){ n=all[all.length-1]; }
				if(n){ e.preventDefault(); n.focus(); n.click(); }
			});
		});
	}
	function idioma(){ if(!document.documentElement.lang) document.documentElement.lang='pt-BR'; }
	function tudo(){ idioma(); marcos(); nomesDeLinks(); carrosseis(); }
	if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded',tudo); } else { tudo(); }
	window.addEventListener('load',tudo);
})();
</script>
	<?php
}, 50 );
