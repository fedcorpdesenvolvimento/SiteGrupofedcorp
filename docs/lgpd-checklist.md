# Checklist de conformidade – LGPD, acessibilidade e redução de riscos

Site: grupofedcorp.com.br · Implementação técnica: outubro/2026 (mu-plugins `fedcorp-lgpd` e `fedcorp-acessibilidade`).

Legenda: ✅ feito no site · 🔶 feito, depende de preenchimento/decisão da empresa · ⬜ ação fora do site (jurídico/gestão).

## 1. Política de Privacidade
- ✅ Página `/politica-de-privacidade/` criada e definida como política oficial do WordPress.
- ✅ Tabela de tratamentos com finalidade e base legal (art. 7º), compartilhamentos, prazos de retenção, direitos, segurança, menores, alterações.
- 🔶 Preencher em `wp-content/mu-plugins/fedcorp-lgpd/config.php`: razão social, **CNPJ**, nome do Encarregado (DPO) e e-mail dedicado (ex.: privacidade@grupofedcorp.com.br).
- ⬜ Revisão por advogado antes da divulgação formal. Guardar versão aprovada e data.

## 2. Termos de serviço no primeiro login
- ✅ Usuários do WordPress: ao entrar, quem não aceitou a versão vigente é levado a `/aceite-de-termos/` e só continua após marcar o aceite (data, versão e hash de IP gravados no perfil; coluna "Termos aceitos" em Usuários).
- ✅ Plataformas externas (Área Restrita e menu lateral): no primeiro clique abre a janela de aceite; a escolha fica no navegador e é renovada quando a versão muda.
- 🔶 Ao alterar os termos, mudar `versao_termos` no config para exigir novo aceite.
- ⬜ As plataformas em domínios próprios (incendiofedcorp, fedcorp.store, multicálculo, sinistro, segimob) têm login próprio: cada uma precisa do seu aceite de termos e política no cadastro/primeiro login. Verificar com cada fornecedor.

## 3. Aviso de cookies
- ✅ Banner com "Aceitar todos", "Apenas necessários" e "Preferências" por categoria; "Gerenciar cookies" no rodapé reabre; escolha válida por 180 dias; acessível por teclado.
- ✅ Google Consent Mode v2 com padrão negado antes da tag do Site Kit; atualização após o aceite.
- ✅ Registro de consentimento (Ferramentas > Consentimentos de cookies): escolhas, data, navegador e hash anônimo; apagado automaticamente após 2 anos.

## 4. Formulários da LGPD
- ✅ Página `/direitos-do-titular/` com formulário dos direitos do art. 18 (acesso, correção, eliminação, portabilidade, revogação, oposição, etc.), protocolo, e-mail ao DPO e confirmação ao titular, prazo de 15 dias (art. 19). Registros em Ferramentas > Solicitações LGPD, apagados após 5 anos.
- ✅ Formulário "Fale Conosco": campo obrigatório de consentimento com link para a política.
- ⬜ Definir quem responde às solicitações e o fluxo interno (localizar dados, confirmar identidade, responder e registrar o status no painel).

## 5. Somente dados necessários (minimização)
- ✅ Comentários e pingbacks desligados; IP de comentários não é gravado; registro público de usuários bloqueado.
- ✅ API REST não lista usuários para visitantes; `?author=N` redireciona; XML-RPC desligado; versão do WordPress oculta.
- ✅ Consentimento e solicitações guardam hash do IP, não o IP.
- 🔶 O "Fale Conosco" envia cópia para `eduardo@omsdesenvolvimento.com.br` (fornecedor externo). Se não for mais necessário, remover o segundo destinatário no Elementor. Se for, formalizar contrato de operador.
- ⬜ Mapear dados tratados fora do site (planilhas, CRM, WhatsApp) e eliminar o que não tem finalidade.

## 6. Scripts de rastreamento
- ✅ Inventário: Google Analytics 4 via Site Kit (tag GT-NBBPTTH4), reCAPTCHA no formulário, Joinchat (WhatsApp, sem cookies de terceiros). Não há pixel do Meta, GTM ou outros.
- ✅ GA4 só grava cookies após consentimento (Consent Mode). Novos scripts devem ser inseridos como `<script type="text/plain" data-fc-consent="statistics|marketing">` para respeitar o aviso.
- 🔶 No Site Kit, ativar "Modo de consentimento" (Configurações > Admin) para reforçar o padrão e, no GA4, manter retenção de dados em 2 meses.

## 7–9. Acessibilidade, contraste e teclado
- ✅ Auditoria axe-core (WCAG 2.1 AA) antes: 5 tipos de violação (contraste 3,2–3,5:1 em azul claro e WhatsApp; logo sem nome; sem marco `main`; sem H1; "pular para o conteúdo" sem destino). Depois: 0 violações nas páginas Home, Fale Conosco, Produtos e Direitos do Titular.
- ✅ Azul de realce ajustado para #1F6FA6 (5,4:1) e WhatsApp para #0F7A6D (5,2:1); foco visível amarelo em todos os controles; marcos banner/main/contentinfo; H1 em cada página; abas navegáveis por setas; carrosséis pausam no foco; alvos de toque ≥ 44 px; respeito a "reduzir movimento".
- ✅ Página `/acessibilidade/` com declaração, limitações conhecidas e canal para reportar barreiras.
- ⬜ Legendas e audiodescrição para o vídeo institucional; revisar PDFs publicados.

## 10. Direitos autorais das imagens
- ✅ Página `/direitos-autorais/`: titularidade do conteúdo, uso nominativo dos logotipos das seguradoras, licenças de banco de imagens, marcas de terceiros e canal de notificação/remoção (notice and takedown).
- ✅ `<meta name="copyright">` e aviso "© ano · razão social · todos os direitos reservados" no rodapé com ano automático.
- ⬜ Manter planilha de origem/licença de cada imagem (Adobe Stock, Freepik, própria, IA); pedir autorização escrita das seguradoras para uso do logotipo ou confirmar cláusula no contrato de corretagem.

## 11. Conformidade com a LGPD (gestão)
- ⬜ Nomear formalmente o Encarregado (art. 41) e publicar contato (feito no site via config).
- ⬜ Registro das operações de tratamento (art. 37) – inventário de dados por processo.
- ⬜ Contratos com operadores: R4C (hospedagem), Google, OMS Desenvolvimento, seguradoras/parceiros; cláusulas de proteção de dados.
- ⬜ Plano de resposta a incidentes (art. 48) e política interna de segurança/senhas.
- ⬜ **Troca de senhas (recomendação registrada em 23/10/2026; a troca será feita manualmente pelo Alberto, no momento que ele definir):** FTP da R4C (`gfcorpalberto`), Acesso Restrito do Apache (`alberto`), administrador do WordPress (`admin`, sugerido criar usuário nominal e remover o `admin`) e SFTP (`gfed_alberto`, se existir). Motivo: essas senhas circularam na conversa de implantação.
- ⬜ Treinamento da equipe de atendimento sobre pedidos de titulares.

## 12. Redução de riscos jurídicos
- ✅ Termos de Uso com: caráter informativo (sem oferta vinculante de seguro), responsabilidade do usuário por credenciais, propriedade intelectual, limitação de responsabilidade, links de terceiros, suspensão, lei aplicável e foro, ressalva do CDC.
- ✅ Separação clara entre site institucional e plataformas com login; aceite registrado.
- ⬜ Verificar exigências SUSEP para corretoras no site (número de registro SUSEP da corretora e dos corretores responsáveis, se aplicável) e incluir no rodapé/"O Grupo".
- ⬜ Revisão jurídica anual das políticas; registrar data no config (`atualizacao`).

## Onde gerenciar
- Textos e dados: `wp-content/mu-plugins/fedcorp-lgpd/config.php` (dados) e `paginas.php` (textos). Para regenerar uma página, apague-a (inclusive da lixeira) e recarregue o site.
- Painel: Ferramentas > Solicitações LGPD e Ferramentas > Consentimentos de cookies.
- Deploy: `python deploy/deploy-ftp.py wp-content/mu-plugins`.
