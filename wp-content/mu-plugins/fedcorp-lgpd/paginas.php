<?php
/**
 * Cria (uma única vez) as páginas legais e define a página de privacidade do WordPress.
 * Para refazer uma página, apague-a (inclusive da lixeira) e recarregue o site.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function fedcorp_lgpd_paginas() {
	return array(
		'politica-de-privacidade' => array( 'titulo' => 'Política de Privacidade',            'conteudo' => 'fedcorp_lgpd_txt_privacidade' ),
		'politica-de-cookies'     => array( 'titulo' => 'Política de Cookies',                'conteudo' => 'fedcorp_lgpd_txt_cookies' ),
		'termos-de-uso'           => array( 'titulo' => 'Termos de Uso',                      'conteudo' => 'fedcorp_lgpd_txt_termos' ),
		'direitos-do-titular'     => array( 'titulo' => 'Direitos do Titular de Dados (LGPD)','conteudo' => 'fedcorp_lgpd_txt_direitos' ),
		'acessibilidade'          => array( 'titulo' => 'Declaração de Acessibilidade',       'conteudo' => 'fedcorp_lgpd_txt_acessibilidade' ),
		'direitos-autorais'       => array( 'titulo' => 'Direitos Autorais e Créditos de Imagens', 'conteudo' => 'fedcorp_lgpd_txt_autorais' ),
		'aceite-de-termos'        => array( 'titulo' => 'Aceite dos Termos de Uso',           'conteudo' => 'fedcorp_lgpd_txt_aceite' ),
	);
}

function fedcorp_lgpd_url( $slug ) {
	$p = get_page_by_path( $slug );
	return $p ? get_permalink( $p ) : home_url( '/' . $slug . '/' );
}

add_action( 'init', 'fedcorp_lgpd_criar_paginas', 20 );
function fedcorp_lgpd_criar_paginas() {
	if ( get_option( 'fedcorp_lgpd_paginas_v' ) === FEDCORP_LGPD_VERSAO ) {
		return;
	}
	foreach ( fedcorp_lgpd_paginas() as $slug => $p ) {
		if ( get_page_by_path( $slug ) ) {
			continue;
		}
		$id = wp_insert_post( array(
			'post_type'      => 'page',
			'post_status'    => 'publish',
			'post_name'      => $slug,
			'post_title'     => $p['titulo'],
			'post_content'   => call_user_func( $p['conteudo'] ),
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_author'    => 1,
		) );
		if ( $id && 'politica-de-privacidade' === $slug ) {
			update_option( 'wp_page_for_privacy_policy', $id );
		}
	}
	if ( ! get_option( 'wp_page_for_privacy_policy' ) ) {
		$pp = get_page_by_path( 'politica-de-privacidade' );
		if ( $pp ) {
			update_option( 'wp_page_for_privacy_policy', $pp->ID );
		}
	}
	update_option( 'fedcorp_lgpd_paginas_v', FEDCORP_LGPD_VERSAO );
}

/* Estilo comum das páginas legais (largura de leitura, tipografia) */
add_action( 'wp_head', function () {
	if ( ! is_page( array_keys( fedcorp_lgpd_paginas() ) ) ) {
		return;
	}
	?>
<style id="fedcorp-legal">
.fc-legal{ max-width:860px; margin:0 auto; padding:56px 20px 72px; font-family:Poppins, system-ui, sans-serif; color:#292929; line-height:1.7; }
.fc-legal h1{ color:#004077; font-size:clamp(28px,4vw,40px); line-height:1.2; margin:0 0 8px; }
.fc-legal h2{ color:#004077; font-size:24px; margin:40px 0 12px; }
.fc-legal h3{ color:#1F6FA6; font-size:19px; margin:28px 0 8px; }
.fc-legal p, .fc-legal li{ font-size:17px; }
.fc-legal .fc-legal__meta{ color:#5a5a5a; font-size:15px; margin-bottom:32px; }
.fc-legal table{ width:100%; border-collapse:collapse; margin:16px 0; font-size:15px; }
.fc-legal th, .fc-legal td{ border:1px solid #e3e8ee; padding:10px 12px; text-align:left; vertical-align:top; }
.fc-legal th{ background:#F3F6F9; color:#004077; }
.fc-legal a{ color:#1F6FA6; text-decoration:underline; }
.fc-legal .fc-legal__aviso{ background:#FFF7D6; border-left:4px solid #FFC203; padding:14px 18px; border-radius:8px; margin:20px 0; }
.fc-legal .fc-legal__links{ display:flex; flex-wrap:wrap; gap:10px; margin:24px 0; }
.fc-legal .fc-legal__links a{ display:inline-block; padding:8px 14px; border-radius:999px; background:#F3F6F9; color:#004077; text-decoration:none; font-weight:500; font-size:14px; }
.fc-legal .fc-legal__links a:hover, .fc-legal .fc-legal__links a:focus-visible{ background:#FFC203; color:#002d54; }
</style>
	<?php
}, 60 );

function fedcorp_lgpd_links_html() {
	$l = array(
		'politica-de-privacidade' => 'Política de Privacidade',
		'politica-de-cookies'     => 'Política de Cookies',
		'termos-de-uso'           => 'Termos de Uso',
		'direitos-do-titular'     => 'Direitos do Titular',
		'acessibilidade'          => 'Acessibilidade',
		'direitos-autorais'       => 'Direitos Autorais',
	);
	$h = '<nav class="fc-legal__links" aria-label="Documentos legais">';
	foreach ( $l as $s => $t ) {
		$h .= '<a href="' . esc_url( fedcorp_lgpd_url( $s ) ) . '">' . esc_html( $t ) . '</a>';
	}
	return $h . '</nav>';
}
add_shortcode( 'fedcorp_links_legais', 'fedcorp_lgpd_links_html' );

function fedcorp_lgpd_cabecalho( $titulo ) {
	return '<div class="fc-legal"><h1>' . esc_html( $titulo ) . '</h1><p class="fc-legal__meta">Última atualização: [fedcorp_dado chave="atualizacao"] · Versão [fedcorp_dado chave="versao_termos"]</p>[fedcorp_links_legais]';
}

/* ------------------------------------------------------------------ TEXTOS */

function fedcorp_lgpd_txt_privacidade() {
	return fedcorp_lgpd_cabecalho( 'Política de Privacidade' ) . <<<'HTML'
<p>Esta Política de Privacidade explica como o <strong>[fedcorp_dado chave="razao_social"]</strong> ("Fedcorp", "nós"), inscrito no CNPJ sob o nº [fedcorp_dado chave="cnpj"], com sede em [fedcorp_dado chave="endereco"], trata os dados pessoais de quem acessa o site [fedcorp_dado chave="site"] e de quem se relaciona com as empresas do grupo, em conformidade com a Lei nº 13.709/2018 (Lei Geral de Proteção de Dados Pessoais – LGPD) e o Marco Civil da Internet (Lei nº 12.965/2014).</p>

<h2>1. Quem somos e como falar conosco</h2>
<p>O Fedcorp atua como <strong>controlador</strong> dos dados pessoais tratados por meio deste site. Nosso Encarregado de Proteção de Dados (DPO) é <strong>[fedcorp_dado chave="dpo_nome"]</strong>, que pode ser contatado pelo e-mail <a href="mailto:[fedcorp_dado chave="dpo_email"]">[fedcorp_dado chave="dpo_email"]</a>.</p>

<h2>2. Quais dados coletamos e por quê</h2>
<table>
<thead><tr><th>Situação</th><th>Dados tratados</th><th>Finalidade</th><th>Base legal (art. 7º da LGPD)</th></tr></thead>
<tbody>
<tr><td>Formulário "Fale Conosco"</td><td>Nome, telefone, e-mail, assunto e mensagem</td><td>Responder à sua solicitação e dar andamento comercial quando você pedir</td><td>Procedimentos preliminares de contrato (inciso V) e legítimo interesse (inciso IX)</td></tr>
<tr><td>Formulário de direitos do titular</td><td>Nome, e-mail, tipo de pedido e descrição</td><td>Atender aos seus direitos previstos no art. 18 da LGPD</td><td>Cumprimento de obrigação legal (inciso II)</td></tr>
<tr><td>Navegação no site</td><td>Endereço IP, páginas visitadas, navegador, dispositivo e identificadores de cookies</td><td>Garantir segurança e funcionamento do site; medir audiência de forma agregada</td><td>Legítimo interesse (inciso IX) para cookies necessários; <strong>consentimento</strong> (inciso I) para cookies de estatística e marketing</td></tr>
<tr><td>Contato por WhatsApp</td><td>Número de telefone e conteúdo da conversa</td><td>Atendimento</td><td>Procedimentos preliminares de contrato (inciso V)</td></tr>
<tr><td>Registro de consentimento de cookies</td><td>Escolhas feitas no aviso de cookies, data/hora, identificador anônimo (hash) e navegador</td><td>Comprovar o consentimento dado</td><td>Cumprimento de obrigação legal (inciso II)</td></tr>
</tbody>
</table>
<p>Coletamos apenas os dados <strong>estritamente necessários</strong> a cada finalidade. Não solicitamos dados sensíveis (art. 5º, II) por meio deste site. Não realizamos decisões automatizadas que afetem seus direitos com base nos dados coletados aqui.</p>

<h2>3. Cookies e tecnologias semelhantes</h2>
<p>Usamos cookies necessários ao funcionamento do site e, somente com seu consentimento, cookies de estatística (Google Analytics 4). Você pode aceitar, recusar ou ajustar suas preferências a qualquer momento no link "Gerenciar cookies" no rodapé. Detalhes na <a href="/politica-de-cookies/">Política de Cookies</a>.</p>

<h2>4. Com quem compartilhamos dados</h2>
<ul>
<li><strong>Empresas do Grupo Fedcorp</strong>, para atendimento integrado das solicitações.</li>
<li><strong>Operadores de tecnologia</strong>: hospedagem do site (R4C), provedor de e-mail e Google (Analytics, reCAPTCHA), que tratam dados em nosso nome e sob contrato.</li>
<li><strong>Seguradoras e parceiros</strong>, apenas quando necessário à cotação ou contratação solicitada por você.</li>
<li><strong>Autoridades públicas</strong>, quando exigido por lei ou ordem judicial.</li>
</ul>
<p>Não vendemos dados pessoais. Alguns operadores podem armazenar dados fora do Brasil; nesses casos, exigimos garantias compatíveis com o art. 33 da LGPD.</p>

<h2>5. Plataformas externas</h2>
<p>Os links da "Área Restrita" e do menu de atalhos levam a plataformas operadas pelo Fedcorp ou por parceiros em domínios próprios (por exemplo, incendiofedcorp.com.br, fedcorp.store, multicalculofedcorp.com.br, sinistro.grupofedcorp.com.br, plataforma.web.segimob.com). Cada plataforma possui política e termos próprios, apresentados no primeiro acesso.</p>

<h2>6. Por quanto tempo guardamos os dados</h2>
<ul>
<li>Mensagens do "Fale Conosco": até 12 meses após o último contato, salvo início de relação comercial.</li>
<li>Pedidos de direitos do titular: 5 anos, para comprovação do atendimento.</li>
<li>Registro de consentimento de cookies: [fedcorp_dado chave="retencao_consent"] anos.</li>
<li>Dados de contrato: pelo prazo legal (em regra, 5 anos após o término, art. 206 do Código Civil e normas da SUSEP).</li>
</ul>

<h2>7. Seus direitos</h2>
<p>Nos termos do art. 18 da LGPD, você pode solicitar: confirmação e acesso aos dados; correção; anonimização, bloqueio ou eliminação; portabilidade; informação sobre compartilhamentos; revogação do consentimento; e oposição a tratamentos. Use o <a href="/direitos-do-titular/">formulário de direitos do titular</a> ou escreva para [fedcorp_dado chave="dpo_email"]. Respondemos em até [fedcorp_dado chave="prazo_resposta"]. Você também pode apresentar reclamação à Autoridade Nacional de Proteção de Dados (ANPD).</p>

<h2>8. Segurança</h2>
<p>Adotamos medidas técnicas e administrativas para proteger os dados: criptografia em trânsito (HTTPS), controle de acesso, senhas fortes, atualizações de segurança e registro de acessos. Em caso de incidente relevante, comunicaremos a ANPD e os titulares, conforme o art. 48 da LGPD.</p>

<h2>9. Crianças e adolescentes</h2>
<p>Este site é destinado a empresas e pessoas maiores de 18 anos. Não coletamos intencionalmente dados de menores.</p>

<h2>10. Alterações</h2>
<p>Podemos atualizar esta política. A data da última revisão aparece no topo. Mudanças relevantes serão destacadas no site.</p>
</div>
HTML;
}

function fedcorp_lgpd_txt_cookies() {
	return fedcorp_lgpd_cabecalho( 'Política de Cookies' ) . <<<'HTML'
<p>Cookies são pequenos arquivos gravados no seu navegador. Esta política descreve quais cookies o site [fedcorp_dado chave="site"] utiliza e como você controla cada categoria.</p>

<h2>Categorias</h2>
<table>
<thead><tr><th>Categoria</th><th>Precisa de consentimento?</th><th>Exemplos</th><th>Duração</th></tr></thead>
<tbody>
<tr><td><strong>Necessários</strong></td><td>Não (legítimo interesse)</td><td><code>fedcorp_consent</code> (guarda suas escolhas), cookies de sessão do WordPress para usuários logados, reCAPTCHA (proteção contra spam no formulário)</td><td>Sessão até 12 meses</td></tr>
<tr><td><strong>Estatísticas</strong></td><td>Sim</td><td>Google Analytics 4 (<code>_ga</code>, <code>_ga_*</code>) – medição de audiência de forma agregada e com IP anonimizado</td><td>Até 2 anos</td></tr>
<tr><td><strong>Marketing</strong></td><td>Sim</td><td>Atualmente não utilizamos cookies de marketing. Se passarmos a usar, serão ativados somente com seu consentimento.</td><td>–</td></tr>
</tbody>
</table>

<h2>Como funciona o aviso de cookies</h2>
<p>No primeiro acesso, o aviso oferece três opções: <strong>Aceitar todos</strong>, <strong>Apenas necessários</strong> ou <strong>Preferências</strong>. Até que você decida, apenas os cookies necessários são utilizados e as ferramentas do Google operam em modo de consentimento negado (Google Consent Mode v2), sem gravar cookies de rastreamento. Sua escolha fica salva por 6 meses e pode ser alterada a qualquer momento em <a href="#" data-fc-abrir-cookies>Gerenciar cookies</a>, no rodapé.</p>

<h2>Como bloquear cookies no navegador</h2>
<p>Você também pode bloquear ou apagar cookies pelas configurações do navegador: <a href="https://support.google.com/chrome/answer/95647?hl=pt-BR" target="_blank" rel="noopener">Chrome</a>, <a href="https://support.mozilla.org/pt-BR/kb/gerencie-configuracoes-de-armazenamento-local-de-s" target="_blank" rel="noopener">Firefox</a>, <a href="https://support.apple.com/pt-br/guide/safari/sfri11471/mac" target="_blank" rel="noopener">Safari</a> e <a href="https://support.microsoft.com/pt-br/microsoft-edge" target="_blank" rel="noopener">Edge</a>. Bloquear cookies necessários pode afetar o funcionamento do site.</p>

<h2>Registro de consentimento</h2>
<p>Para comprovar o consentimento, guardamos as escolhas feitas, a data e hora, o navegador utilizado e um identificador anônimo (hash irreversível do endereço IP) por [fedcorp_dado chave="retencao_consent"] anos. Não guardamos o IP em si.</p>
</div>
HTML;
}

function fedcorp_lgpd_txt_termos() {
	return fedcorp_lgpd_cabecalho( 'Termos de Uso' ) . <<<'HTML'
<p>Estes Termos regulam o uso do site [fedcorp_dado chave="site"] e das plataformas a ele vinculadas, mantidos por <strong>[fedcorp_dado chave="razao_social"]</strong>, CNPJ [fedcorp_dado chave="cnpj"]. Ao navegar no site ou acessar as plataformas, você concorda com estes Termos e com a <a href="/politica-de-privacidade/">Política de Privacidade</a>.</p>

<h2>1. Finalidade do site</h2>
<p>O site tem caráter informativo e institucional, apresentando soluções em seguros, serviços e tecnologia voltadas a administradoras de imóveis, condomínios, imobiliárias e empresas. As informações não constituem oferta vinculante nem proposta de seguro; condições, coberturas, prêmios e prazos são definidos exclusivamente nas apólices e contratos emitidos pelas seguradoras e parceiros.</p>

<h2>2. Plataformas e área restrita</h2>
<p>O acesso às plataformas (incêndio locação, comercial, multicálculo, seguro fiança, sinistros, marketplace) é pessoal e intransferível, mediante credenciais fornecidas pelo Fedcorp ou pelo parceiro. O usuário é responsável por manter suas credenciais em sigilo, por todas as operações realizadas com elas e por comunicar imediatamente qualquer uso indevido. No primeiro acesso, o usuário deve ler e aceitar estes Termos; versões novas exigirão novo aceite.</p>

<h2>3. Obrigações do usuário</h2>
<ul>
<li>Fornecer informações verdadeiras, completas e atualizadas.</li>
<li>Não utilizar o site ou as plataformas para fins ilícitos, fraudulentos ou que violem direitos de terceiros.</li>
<li>Não tentar acessar áreas, dados ou sistemas não autorizados, nem interferir no funcionamento do site (varreduras, injeção de código, sobrecarga, engenharia reversa).</li>
<li>Não reproduzir, distribuir ou explorar comercialmente conteúdos do site sem autorização por escrito.</li>
</ul>

<h2>4. Propriedade intelectual</h2>
<p>Marcas, logotipos, textos, layouts, códigos, vídeos e imagens do site pertencem ao Fedcorp ou são licenciados a ele. Logotipos de seguradoras e parceiros pertencem aos respectivos titulares e são exibidos apenas para identificar a relação comercial existente. Consulte a página <a href="/direitos-autorais/">Direitos Autorais e Créditos de Imagens</a>.</p>

<h2>5. Limitação de responsabilidade</h2>
<p>O Fedcorp emprega esforços razoáveis para manter o site disponível, atualizado e seguro, mas não garante funcionamento ininterrupto ou livre de erros. Na extensão permitida pela lei, o Fedcorp não responde por: (a) danos decorrentes de indisponibilidade, falhas de conexão ou do dispositivo do usuário; (b) conteúdo e serviços de sites de terceiros acessados por links; (c) decisões tomadas pelo usuário com base em informações de caráter geral do site; (d) uso indevido de credenciais pelo próprio usuário ou por quem ele as tenha compartilhado. Nada nestes Termos exclui responsabilidades que não possam ser limitadas pelo Código de Defesa do Consumidor.</p>

<h2>6. Links para terceiros</h2>
<p>Links externos são oferecidos por conveniência. O Fedcorp não controla nem endossa o conteúdo, as políticas ou as práticas desses sites.</p>

<h2>7. Privacidade e proteção de dados</h2>
<p>O tratamento de dados pessoais segue a <a href="/politica-de-privacidade/">Política de Privacidade</a> e a LGPD. Nas plataformas, o Fedcorp pode atuar como controlador ou como operador em nome da administradora ou imobiliária cliente, conforme o contrato firmado.</p>

<h2>8. Suspensão e encerramento</h2>
<p>O Fedcorp pode suspender ou encerrar o acesso do usuário que violar estes Termos, a lei ou direitos de terceiros, sem prejuízo das medidas cabíveis.</p>

<h2>9. Alterações</h2>
<p>Estes Termos podem ser alterados a qualquer tempo. A versão vigente, com data e número, está sempre publicada nesta página.</p>

<h2>10. Lei aplicável e foro</h2>
<p>Aplica-se a legislação brasileira. Fica eleito o foro da Comarca da Capital do Estado do Rio de Janeiro para dirimir controvérsias, ressalvado o foro do domicílio do consumidor quando aplicável.</p>

<p><strong>Contato:</strong> [fedcorp_dado chave="email_contato"] · [fedcorp_dado chave="telefone"] · [fedcorp_dado chave="endereco"]</p>
</div>
HTML;
}

function fedcorp_lgpd_txt_direitos() {
	return fedcorp_lgpd_cabecalho( 'Direitos do Titular de Dados (LGPD)' ) . <<<'HTML'
<p>A Lei Geral de Proteção de Dados (art. 18) garante a você, titular, os direitos abaixo. Use o formulário desta página para exercê-los. Responderemos em até <strong>[fedcorp_dado chave="prazo_resposta"]</strong>, de forma gratuita. Para proteger seus dados, poderemos pedir a confirmação da sua identidade antes de atender ao pedido.</p>
<ul>
<li><strong>Confirmação e acesso</strong>: saber se tratamos seus dados e obter uma cópia.</li>
<li><strong>Correção</strong>: corrigir dados incompletos, inexatos ou desatualizados.</li>
<li><strong>Anonimização, bloqueio ou eliminação</strong> de dados desnecessários, excessivos ou tratados em desconformidade com a lei.</li>
<li><strong>Portabilidade</strong> dos dados a outro fornecedor, observada a regulamentação da ANPD.</li>
<li><strong>Eliminação</strong> dos dados tratados com base no seu consentimento.</li>
<li><strong>Informação</strong> sobre as entidades com as quais compartilhamos seus dados.</li>
<li><strong>Informação sobre a possibilidade de não consentir</strong> e as consequências da negativa.</li>
<li><strong>Revogação do consentimento</strong> e <strong>oposição</strong> a tratamentos realizados em desconformidade com a lei.</li>
</ul>
[fedcorp_lgpd_form]
<p>Se preferir, envie seu pedido diretamente ao Encarregado de Proteção de Dados: <a href="mailto:[fedcorp_dado chave="dpo_email"]">[fedcorp_dado chave="dpo_email"]</a>. Caso não fique satisfeito com a resposta, você pode recorrer à <a href="https://www.gov.br/anpd/" target="_blank" rel="noopener">Autoridade Nacional de Proteção de Dados (ANPD)</a>.</p>
</div>
HTML;
}

function fedcorp_lgpd_txt_acessibilidade() {
	return fedcorp_lgpd_cabecalho( 'Declaração de Acessibilidade' ) . <<<'HTML'
<p>O Grupo Fedcorp quer que todas as pessoas, inclusive pessoas com deficiência, consigam usar este site. Nosso compromisso segue a Lei Brasileira de Inclusão (Lei nº 13.146/2015, art. 63), o Decreto nº 5.296/2004 e as diretrizes <strong>WCAG 2.1 nível AA</strong> do W3C.</p>

<h2>O que já está implementado</h2>
<ul>
<li>Link "Pular para o conteúdo" no início de cada página e marcos de navegação (cabeçalho, conteúdo principal, rodapé) para leitores de tela.</li>
<li>Navegação completa por teclado, com indicação visível de foco em links, botões, menus, abas e carrosséis. Os carrosséis pausam ao receber foco ou o ponteiro.</li>
<li>Contraste mínimo de 4,5:1 entre texto e fundo nos elementos corrigidos (botões, títulos e etiquetas em azul claro e botão do WhatsApp).</li>
<li>Textos alternativos nas imagens informativas; imagens decorativas marcadas para serem ignoradas por leitores de tela.</li>
<li>Vídeo da página inicial inicia sem som, com botão acessível para ativar o áudio; animações reduzidas para quem configura "reduzir movimento" no sistema.</li>
<li>Idioma da página declarado (português do Brasil), títulos hierárquicos e formulários com rótulos.</li>
</ul>

<h2>Limitações conhecidas</h2>
<ul>
<li>Alguns conteúdos em PDF ou em plataformas de parceiros podem não atender integralmente às diretrizes. Estamos trabalhando com os parceiros para corrigi-los.</li>
<li>O vídeo institucional ainda não possui legendas ou audiodescrição. Previsão de inclusão: próxima atualização do vídeo.</li>
</ul>

<h2>Atalhos úteis</h2>
<p>Use <kbd>Tab</kbd> e <kbd>Shift</kbd>+<kbd>Tab</kbd> para navegar entre elementos, <kbd>Enter</kbd> ou <kbd>Espaço</kbd> para ativar, <kbd>Esc</kbd> para fechar menus e janelas, e as setas para percorrer abas e carrosséis. Para aumentar o texto, use <kbd>Ctrl</kbd>+<kbd>+</kbd> (ou <kbd>Cmd</kbd>+<kbd>+</kbd> no Mac).</p>

<h2>Encontrou uma barreira?</h2>
<p>Escreva para <a href="mailto:[fedcorp_dado chave="email_contato"]">[fedcorp_dado chave="email_contato"]</a> informando a página e a dificuldade encontrada. Responderemos em até 5 dias úteis. Esta declaração foi revisada em [fedcorp_dado chave="atualizacao"].</p>
</div>
HTML;
}

function fedcorp_lgpd_txt_autorais() {
	return fedcorp_lgpd_cabecalho( 'Direitos Autorais e Créditos de Imagens' ) . <<<'HTML'
<p>© <span data-fc-ano></span> [fedcorp_dado chave="razao_social"]. Todos os direitos reservados. O conteúdo deste site é protegido pela Lei de Direitos Autorais (Lei nº 9.610/1998), pela Lei de Propriedade Industrial (Lei nº 9.279/1996) e pela Lei do Software (Lei nº 9.609/1998).</p>

<h2>Conteúdo próprio</h2>
<p>Textos, marca, logotipo, identidade visual, vídeo institucional, layout e código do site são de titularidade do Grupo Fedcorp ou foram produzidos sob encomenda com cessão de direitos. É proibida a reprodução, total ou parcial, sem autorização prévia e por escrito, exceto para citação com indicação da fonte.</p>

<h2>Logotipos de seguradoras e parceiros</h2>
<p>Os logotipos exibidos na seção "Seguradoras parceiras" (Porto Seguro, Allianz, Bradesco Seguros, Pottencial, HDI, Chubb, AXA, Brasilcap, Tokio Marine, Too Seguros, Suhai, Amil, Yelum, Azul Seguros, Mapfre, entre outros) são marcas registradas de seus respectivos titulares. Eles são apresentados exclusivamente para <strong>identificar a relação comercial existente</strong> entre o Grupo Fedcorp e cada empresa, sem implicar endosso, patrocínio ou associação além dessa relação. Nenhum logotipo é alterado. Caso um titular deseje a remoção ou a substituição do seu logotipo, basta solicitar pelo contato abaixo.</p>

<h2>Imagens de banco e ilustrações</h2>
<p>Fotografias e ilustrações de terceiros utilizadas no site foram obtidas de bancos de imagens sob licença que permite o uso comercial, ou geradas digitalmente para o Fedcorp. O crédito do autor é informado na própria imagem (legenda ou atributo) sempre que a licença exigir. A lista de créditos é mantida pela equipe de comunicação e pode ser solicitada pelo contato abaixo.</p>

<h2>Marcas de terceiros</h2>
<p>WordPress, Elementor, Google, WhatsApp e demais nomes de produtos citados são marcas de seus titulares, utilizadas apenas para descrever tecnologias ou canais de atendimento.</p>

<h2>Denúncia de violação ou pedido de remoção</h2>
<p>Se você acredita que algum conteúdo deste site viola direitos seus, envie para <a href="mailto:[fedcorp_dado chave="email_contato"]">[fedcorp_dado chave="email_contato"]</a>: identificação do conteúdo (link), comprovação da titularidade e seus dados de contato. Analisaremos e responderemos em até 10 dias úteis, removendo o conteúdo quando cabível.</p>
</div>
HTML;
}

function fedcorp_lgpd_txt_aceite() {
	return '<div class="fc-legal"><h1>Aceite dos Termos de Uso</h1><p>Antes de continuar, leia e aceite os <a href="/termos-de-uso/" target="_blank" rel="noopener">Termos de Uso</a> e a <a href="/politica-de-privacidade/" target="_blank" rel="noopener">Política de Privacidade</a>.</p>[fedcorp_aceite_termos]</div>';
}
