<?php
add_action('after_setup_theme', function () {
    add_theme_support('title-tag');
    // Customizer settings for theme mode
    add_action('customize_register', function ($wp_customize) {
        $wp_customize->add_section('cf_theme_section', [
            'title' => __('Contact Form Theme', 'contact-form-wp'),
            'priority' => 30,
        ]);

        $wp_customize->add_setting('cf_theme_mode', [
            'default' => 'light',
            'transport' => 'refresh',
            'sanitize_callback' => function ($value) {
                return in_array($value, ['light', 'dark'], true) ? $value : 'light';
            }
        ]);

        $wp_customize->add_control('cf_theme_mode_control', [
            'label' => __('Theme Mode', 'contact-form-wp'),
            'section' => 'cf_theme_section',
            'settings' => 'cf_theme_mode',
            'type' => 'radio',
            'choices' => [
                'light' => __('Light', 'contact-form-wp'),
                'dark' => __('Dark', 'contact-form-wp'),
            ],
        ]);
    });
});

add_action('wp_enqueue_scripts', function () {
    $theme_uri = get_template_directory_uri();
    $theme_dir = get_template_directory();

    // CSS principal do tema e do site estático (com cache-busting por filemtime)
    $style_css = $theme_dir . '/style.css';
    $style_ver = file_exists($style_css) ? filemtime($style_css) : '1.0';
    wp_enqueue_style('contact-form-style', $theme_uri . '/style.css', [], $style_ver);
    $main_css = $theme_dir . '/assets/css/main.css';
    if (file_exists($main_css)) {
        $main_ver = filemtime($main_css);
        wp_enqueue_style('contact-form-main', $theme_uri . '/assets/css/main.css', ['contact-form-style'], $main_ver);
    }

    // CSS inline que estava no <head> do index.html
    $inline_css = '.eulen-button-style[href^="mailto"]{user-select:text!important;-webkit-user-select:text!important;-moz-user-select:text!important;-ms-user-select:text!important;cursor:text!important;}';
    wp_add_inline_style('contact-form-style', $inline_css);

    // JS (com cache-busting por filemtime)
    $search_js = $theme_dir . '/assets/js/search.js';
    $script_js = $theme_dir . '/assets/js/script.js';
    $search_ver = file_exists($search_js) ? filemtime($search_js) : '1.0';
    $script_ver = file_exists($script_js) ? filemtime($script_js) : '1.0';
    wp_enqueue_script('contact-form-search', $theme_uri . '/assets/js/search.js', [], $search_ver, true);
    wp_enqueue_script('contact-form-script', $theme_uri . '/assets/js/script.js', ['contact-form-search'], $script_ver, true);

    // Passar config.json URL e modo do tema para o JS
    $config_url = $theme_uri . '/config.json';
    $mode = get_theme_mod('cf_theme_mode', 'light');
    wp_localize_script('contact-form-script', 'CFTheme', [
        'configUrl' => $config_url,
        'mode' => $mode,
    ]);

    // JS inline do final do index.html e utilitários inline
    $inline_js = <<<'JS'
// copyToClipboard (estava inline na seção "Dúvidas")
function copyToClipboard() {
  const telegramUser = "@suporte_eulen";
  const tempElement = document.createElement('textarea');
  tempElement.value = telegramUser;
  tempElement.setAttribute('readonly', '');
  tempElement.style.position = 'absolute';
  tempElement.style.left = '-9999px';
  document.body.appendChild(tempElement);
  tempElement.select();
  document.execCommand('copy');
  document.body.removeChild(tempElement);
  if (navigator.clipboard) {
    navigator.clipboard.writeText(telegramUser).catch(err => {
      console.error('Erro ao copiar com Clipboard API:', err);
    });
  }
  const tooltip = document.getElementById('copyTooltip');
  if (tooltip) {
    tooltip.style.opacity = '1';
    setTimeout(() => { tooltip.style.opacity = '0'; }, 1000);
  }
}

// Script para carregar os templates (do final do index.html)
document.addEventListener('DOMContentLoaded', function() {
  const meetingTemplate = document.getElementById('meeting-consent-template');

  // P2P
  const p2pContainer = document.getElementById('p2p-meeting-consent');
  if (p2pContainer && meetingTemplate) {
    const p2pClone = meetingTemplate.content.cloneNode(true);
    const checkbox = p2pClone.querySelector('#consent-checkbox');
    const errorMsg = p2pClone.querySelector('#consent-error');
    checkbox.id = 'p2p-meeting-consent-checkbox';
    checkbox.name = 'p2pMeetingConsent';
    errorMsg.id = 'p2pMeetingConsent-error';
    const label = p2pClone.querySelector('label[for="consent-checkbox"]');
    label.setAttribute('for', 'p2p-meeting-consent-checkbox');
    p2pContainer.appendChild(p2pClone);
  }

  // Comércio
  const comercioContainer = document.getElementById('comercio-meeting-consent');
  if (comercioContainer && meetingTemplate) {
    const comercioClone = meetingTemplate.content.cloneNode(true);
    const checkbox = comercioClone.querySelector('#consent-checkbox');
    const errorMsg = comercioClone.querySelector('#consent-error');
    checkbox.id = 'comercio-meeting-consent-checkbox';
    checkbox.name = 'comercioMeetingConsent';
    errorMsg.id = 'comercioMeetingConsent-error';
    const label = comercioClone.querySelector('label[for="consent-checkbox"]');
    label.setAttribute('for', 'comercio-meeting-consent-checkbox');
    comercioContainer.appendChild(comercioClone);
  }

  // Plataforma
  const plataformaContainer = document.getElementById('plataforma-meeting-consent');
  if (plataformaContainer && meetingTemplate) {
    const plataformaClone = meetingTemplate.content.cloneNode(true);
    const checkbox = plataformaClone.querySelector('#consent-checkbox');
    const errorMsg = plataformaClone.querySelector('#consent-error');
    checkbox.id = 'plataforma-meeting-consent-checkbox';
    checkbox.name = 'plataformaMeetingConsent';
    errorMsg.id = 'plataformaMeetingConsent-error';
    const label = plataformaClone.querySelector('label[for="consent-checkbox"]');
    label.setAttribute('for', 'plataforma-meeting-consent-checkbox');
    plataformaContainer.appendChild(plataformaClone);
  }

  // Template para informações do usuário
  const userInfoTemplate = document.getElementById('user-info-template');
  const userInfoContainer = document.getElementById('user-info-container');
  if (userInfoContainer && userInfoTemplate) {
    const userInfoClone = userInfoTemplate.content.cloneNode(true);
    userInfoContainer.appendChild(userInfoClone);
  }

  // Navegação: ir do passo de valor (2 ou 2-depix) para o passo 2.6 (rede/carteira)
  document.querySelectorAll('.go-wallet-step').forEach(btn => {
    // Passa para o step 2.6 sem interferir no back
    btn.addEventListener('click', () => {
      const steps = Array.from(document.querySelectorAll('.form-step'));
      // Capturar estado atual (ativo BTC ou DePix) e valores
      const isBTC = document.documentElement.classList.contains('asset-btc');
      const isDPX = document.documentElement.classList.contains('asset-depix');
      const brlIn = isBTC ? document.getElementById('desiredAmountBRL') : document.getElementById('desiredAmountBRL_DPX');
      const outIn = isBTC ? document.getElementById('convertedAmountBTC') : document.getElementById('convertedAmountDPX');
      const feesElActive = document.querySelector('.form-step.active .amount-fees');

      const brlVal = brlIn ? brlIn.value : '';
      const outVal = outIn ? outIn.value : '';
      const feesHtml = feesElActive ? feesElActive.innerHTML : 'Taxa: <span class="fee-expl">(R$1 transação + 5%)</span>';

      // Ir para 2.6
      const targetIdx = steps.findIndex(s => s.dataset.step === '2.6');
      if (targetIdx !== -1) {
        steps.forEach(s => s.classList.remove('active'));
        steps[targetIdx].classList.add('active');
      }

      // Preencher confirm de valores fixos
      const confirmBRL = document.getElementById('confirmBRL');
      const confirmOut = document.getElementById('confirmOut');
      const confirmPrefix = document.getElementById('confirmPrefix');
      const confirmSuffix = document.getElementById('confirmSuffix');
      const confirmFees = document.getElementById('confirmFees');
      if (confirmBRL) confirmBRL.value = brlVal || '';
      if (confirmOut) confirmOut.value = outVal || '';
      if (confirmFees) confirmFees.innerHTML = feesHtml;
      if (isBTC) {
        if (confirmPrefix) confirmPrefix.textContent = '₿';
        if (confirmSuffix) confirmSuffix.textContent = 'BTC';
      } else if (isDPX) {
        if (confirmPrefix) confirmPrefix.textContent = 'Đ';
        if (confirmSuffix) confirmSuffix.textContent = 'DPX';
      }

      // Renderizar chips de estado (Ativo -> Quantia -> [Rede])
      const renderChips = (root) => {
        if (!root) return;
        root.innerHTML = '';
        const chipWrap = document.createElement('div');
        chipWrap.className = 'radio-group state-chips';

        const chipAsset = document.createElement('label');
        chipAsset.textContent = isBTC ? 'Bitcoin' : (isDPX ? 'DePix' : 'Ativo');
        chipWrap.appendChild(chipAsset);

        // seta e quantia (usar somente a quantia convertida no ativo)
        if (outVal) {
          const sep = document.createElement('span'); sep.className = 'chip-sep'; sep.textContent = '→'; chipWrap.appendChild(sep);
          const chipOut = document.createElement('label');
          chipOut.textContent = isBTC ? `${outVal} BTC` : `${outVal} DPX`;
          chipWrap.appendChild(chipOut);
        }
        root.appendChild(chipWrap);
      };

      // Chips no 2.6
      renderChips(document.querySelector('.form-step[data-step="2.6"] .step-state-chips'));
    });
  });

  // Atualiza chips encadeados no 2.6: Bitcoin -> Lightning -> endereço
  const networkRadioLiquid = document.getElementById('network-liquid');
  const networkRadioLightning = document.getElementById('network-lightning');
  const walletInput = document.getElementById('wallet-address');
  const walletHint = document.getElementById('wallet-hint');
  function updateFlowChips() {
    const root = document.querySelector('.form-step[data-step="2.6"] .step-state-chips');
    if (!root) return;
    const isBTC = document.documentElement.classList.contains('asset-btc');
    const isDPX = document.documentElement.classList.contains('asset-depix');
    const currentNetwork = networkRadioLightning?.checked ? 'lightning' : (networkRadioLiquid?.checked ? 'liquid' : '');
    const networkLabel = currentNetwork === 'lightning' ? 'Lightning' : currentNetwork === 'liquid' ? 'Liquid' : '';

    root.innerHTML = '';
    const wrap = document.createElement('div');
    wrap.className = 'radio-group state-chips';
    const assetChip = document.createElement('label');
    assetChip.textContent = isBTC ? 'Bitcoin' : (isDPX ? 'DePix' : 'Ativo');
    wrap.appendChild(assetChip);
    // quantia do ativo (pegar do campo convertido)
    let amountVal = '';
    const outBtc = document.getElementById('convertedAmountBTC');
    const outDpx = document.getElementById('convertedAmountDPX');
    if (isBTC && outBtc && outBtc.value) amountVal = `${outBtc.value} BTC`;
    if (isDPX && outDpx && outDpx.value) amountVal = `${outDpx.value} DPX`;
    if (amountVal) {
      const sepAmt = document.createElement('span'); sepAmt.className = 'chip-sep'; sepAmt.textContent = '→'; wrap.appendChild(sepAmt);
      const amtChip = document.createElement('label'); amtChip.textContent = amountVal; wrap.appendChild(amtChip);
    }
    if (networkLabel) {
      const sep = document.createElement('span'); sep.className = 'chip-sep'; sep.textContent = '→'; wrap.appendChild(sep);
      const netChip = document.createElement('label'); netChip.textContent = networkLabel; wrap.appendChild(netChip);
    }
    // endereço só aparecerá no 2.7
    root.appendChild(wrap);
  }
  networkRadioLiquid?.addEventListener('change', updateFlowChips);
  networkRadioLightning?.addEventListener('change', updateFlowChips);
  walletInput?.addEventListener('input', updateFlowChips);
  updateFlowChips();

  // Atualiza placeholder e dica conforme rede escolhida
  function refreshWalletUIByNetwork(){
    if (!walletInput || !walletHint) return;
    const isLiquid = networkRadioLiquid?.checked;
    if (isLiquid) {
      walletInput.placeholder = 'lq1… ou CT…';
    } else {
      walletInput.placeholder = 'bc1…';
      walletHint.textContent = '';
    }
  }
  refreshWalletUIByNetwork();
  networkRadioLiquid?.addEventListener('change', refreshWalletUIByNetwork);
  networkRadioLightning?.addEventListener('change', refreshWalletUIByNetwork);

  // Avanço do 2.6 (rede) para 2.7 (endereço): deixa validação para o script principal
  document.querySelector('.form-step[data-step="2.6"] .next-btn')?.addEventListener('click', () => {
    const selected = (networkRadioLiquid?.checked || networkRadioLightning?.checked);
    const error = document.getElementById('networkChoice-error');
    if (!selected) { if (error) error.classList.add('visible'); return; }
    if (error) error.classList.remove('visible');
    const from = document.querySelector('.form-step[data-step="2.6"] .step-state-chips');
    const to = document.querySelector('.form-step[data-step="2.7"] .step-state-chips');
    if (from && to) to.innerHTML = from.innerHTML;
  });

  // Voltar do 2.7 para 2.6 mantém o estado
  document.querySelector('.form-step[data-step="2.7"] .prev-btn')?.addEventListener('click', () => {
    const steps = Array.from(document.querySelectorAll('.form-step'));
    const targetIdx = steps.findIndex(s => s.dataset.step === '2.6');
    if (targetIdx !== -1) {
      steps.forEach(s => s.classList.remove('active'));
      steps[targetIdx].classList.add('active');
    }
  });
});

function copySecurityEmail() {
  const emailText = "security@depix.info";
  const tempElement = document.createElement('textarea');
  tempElement.value = emailText;
  tempElement.setAttribute('readonly', '');
  tempElement.style.position = 'absolute';
  tempElement.style.left = '-9999px';
  document.body.appendChild(tempElement);
  tempElement.select();
  document.execCommand('copy');
  document.body.removeChild(tempElement);
  if (navigator.clipboard) {
    navigator.clipboard.writeText(emailText).catch(err => {
      console.error('Erro ao copiar com Clipboard API:', err);
    });
  }
  const tooltip = document.getElementById('securityCopyTooltip');
  if (tooltip) {
    tooltip.style.opacity = '1';
    setTimeout(() => { tooltip.style.opacity = '0'; }, 1000);
  }
}

function copyJobsLinkFunction() {
  const jobsLink = "https://jobs.eulen.app";
  const tempElement = document.createElement('textarea');
  tempElement.value = jobsLink;
  tempElement.setAttribute('readonly', '');
  tempElement.style.position = 'absolute';
  tempElement.style.left = '-9999px';
  document.body.appendChild(tempElement);
  tempElement.select();
  document.execCommand('copy');
  document.body.removeChild(tempElement);
  if (navigator.clipboard) {
    navigator.clipboard.writeText(jobsLink).catch(err => {
      console.error('Erro ao copiar com Clipboard API:', err);
    });
  }
  const tooltip = document.getElementById('jobsCopyTooltip');
  if (tooltip) {
    tooltip.style.opacity = '1';
    setTimeout(() => { tooltip.style.opacity = '0'; }, 1000);
  }
}

// Simular avanço ao escolher opções
function triggerNextStep(currentStepElement) {
  const anyNextButton = document.querySelector('.next-btn');
  if (anyNextButton && typeof anyNextButton.onclick === 'function') {
    anyNextButton.onclick.apply(anyNextButton);
  } else if (anyNextButton) {
    const clickEvent = new Event('click', { bubbles: true, cancelable: true });
    anyNextButton.dispatchEvent(clickEvent);
    console.log("Simulando clique no botão 'next' para avançar.");
  } else {
    console.error("Não foi possível encontrar a lógica do botão 'next' para avançar automatically.");
  }
}

document.querySelectorAll('.form-step[data-step="1"] input[name="category"]').forEach(radio => {
  radio.addEventListener('change', function() {
    if (this.checked) {
      const currentStepElement = this.closest('.form-step');
      if (currentStepElement) triggerNextStep(currentStepElement);
    }
  });
});

document.querySelectorAll('.form-step[data-step="2"] input[name="profileType"]').forEach(radio => {
  radio.addEventListener('change', function() {
    if (this.checked) {
      const currentStepElement = this.closest('.form-step');
      if (currentStepElement) triggerNextStep(currentStepElement);
    }
  });
});
JS;
    wp_add_inline_script('contact-form-script', $inline_js);

    // Adiciona classe de modo no body via inline script simples
    $mode_js = "document.documentElement.classList.add('cf-mode-" . esc_js($mode) . "');";
    wp_add_inline_script('contact-form-script', $mode_js, 'before');
    
    // Inline JS para color theme switching por ativo (só barra/progress e prefixos)
    $asset_js = <<<'JS'
document.addEventListener('DOMContentLoaded', () => {
  const progress = document.querySelector('.progress-bar .progress');
  const stepAmount = document.querySelector('.form-step[data-step="2"][data-kind="amount"]');
  const prefix = document.getElementById('amountPrefix');
  const suffix = document.getElementById('amountSuffix');
  const convPrefix = document.getElementById('convertedPrefix');
  const convSuffix = document.getElementById('convertedSuffix');

  /**
   * Inicializa a linha de taxas no slide ativo.
  * Formato: "Taxa: 0.00000000 BTC (R$1 transação + 5%)" ou "Taxa: 0.00 DPX (R$1 transação + 5%)"
   */
  function setDefaultFees(){
    const feesEl = document.querySelector('.form-step.active .amount-fees');
    if (!feesEl) return;
    feesEl.innerHTML = 'Taxa: <span class="fee-expl">(R$1 transação + 5%)</span>';
  }

  function applyTheme(asset){
    if (!progress) return;
    document.documentElement.classList.remove('asset-btc','asset-depix');
    if (asset === 'btc'){
      document.documentElement.classList.add('asset-btc');
      if (prefix) prefix.textContent = 'R$';
      if (suffix) suffix.textContent = '';
      if (convPrefix) convPrefix.textContent = '₿';
      if (convSuffix) convSuffix.textContent = 'BTC';
    } else if (asset === 'depix'){
      document.documentElement.classList.add('asset-depix');
      if (prefix) prefix.textContent = 'R$';
      if (suffix) suffix.textContent = '';
      if (convPrefix) convPrefix.textContent = 'Đ';
      if (convSuffix) convSuffix.textContent = 'DPX';
    } else {
      // estado neutro ao voltar
      if (prefix) prefix.textContent = 'R$';
      if (suffix) suffix.textContent = '';
      if (convPrefix) convPrefix.textContent = '₿';
      if (convSuffix) convSuffix.textContent = 'BTC';
    }
  }

  const btcRadio = document.getElementById('category-onboarding');
  const dpxRadio = document.getElementById('category-security');
  [btcRadio, dpxRadio].forEach(r => r && r.addEventListener('change', () => {
    if (btcRadio.checked){
      applyTheme('btc');
      // ir para step 2 (btc)
      const steps = Array.from(document.querySelectorAll('.form-step'));
      const targetIdx = steps.findIndex(s => s.dataset.step === '2');
      if (targetIdx !== -1) {
        steps.forEach(s => s.classList.remove('active'));
        steps[targetIdx].classList.add('active');
        setDefaultFees();
      }
      // Chips no step 2 (BTC)
      const chipsRoot = document.querySelector('.form-step[data-step="2"] .step-state-chips');
      if (chipsRoot) { chipsRoot.innerHTML = '<div class="radio-group state-chips"><label>Bitcoin</label></div>'; }
    } else if (dpxRadio.checked){
      applyTheme('depix');
      // ir para step 2 depix
      const steps = Array.from(document.querySelectorAll('.form-step'));
      const targetIdx = steps.findIndex(s => s.dataset.step === '2-depix');
      if (targetIdx !== -1) {
        steps.forEach(s => s.classList.remove('active'));
        steps[targetIdx].classList.add('active');
        setDefaultFees();
      }
      // Chips no step 2-depix (DPX)
      const chipsRoot = document.querySelector('.form-step[data-step="2-depix"] .step-state-chips');
      if (chipsRoot) { chipsRoot.innerHTML = '<div class="radio-group state-chips"><label>DePix</label></div>'; }
    }
  }));

  // Estado inicial: não aplica tema até haver seleção
  if (btcRadio?.checked) { applyTheme('btc'); setDefaultFees(); }
  else if (dpxRadio?.checked) { applyTheme('depix'); setDefaultFees(); }

  // ao clicar em voltar, remove o tema do ativo
  document.querySelectorAll('.prev-btn').forEach(btn => btn.addEventListener('click', () => applyTheme(null)));
});
JS;
    wp_add_inline_script('contact-form-script', $asset_js);

    // Inline JS: conversão BRL -> BTC (CoinGecko) em tempo real
    $convert_js = <<<'JS'
document.addEventListener('DOMContentLoaded', () => {
  const amountInput = document.getElementById('desiredAmountBRL');
  const outBtc = document.getElementById('convertedAmountBTC');
  const amountInputDPX = document.getElementById('desiredAmountBRL_DPX');
  const outDPX = document.getElementById('convertedAmountDPX');
  function getFeesNote(){
    return document.querySelector('.form-step.active .amount-fees') || document.querySelector('.amount-fees');
  }
  if (!amountInput || !outBtc) return;

  let lastFetchAt = 0; let cachedBtcBrl = null; let debounce;
  function saveCacheLS(price){
    try { localStorage.setItem('btcBrlPrice', String(price)); localStorage.setItem('btcBrlAt', String(Date.now())); } catch(_) {}
  }
  function loadCacheLS(maxAgeMs){
    try {
      const at = Number(localStorage.getItem('btcBrlAt') || 0);
      const price = Number(localStorage.getItem('btcBrlPrice') || 0);
      if (price > 0 && Date.now() - at < maxAgeMs) return price;
    } catch(_) {}
    return null;
  }
  async function getBtcPriceBRL(){
    const now = Date.now();
    // 1) memory cache (60s)
    if (cachedBtcBrl && now - lastFetchAt < 60000) return cachedBtcBrl;
    // 2) localStorage cache (5 min)
    const ls = loadCacheLS(5 * 60 * 1000);
    if (ls) { cachedBtcBrl = ls; lastFetchAt = now; return cachedBtcBrl; }
    // 3) network with timeout + retries
    const endpoint = 'https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=brl';
    const fetchWithTimeout = (ms) => new Promise((resolve, reject) => {
      const ctrl = new AbortController();
      const t = setTimeout(() => { ctrl.abort(); reject(new Error('timeout')); }, ms);
      fetch(endpoint, { signal: ctrl.signal }).then(r => { clearTimeout(t); resolve(r); }).catch(err => { clearTimeout(t); reject(err); });
    });
    const attempts = [1200, 2000, 3000];
    for (const ms of attempts) {
      try {
        const r = await fetchWithTimeout(ms);
        if (!r.ok) continue;
        const j = await r.json();
        if (j?.bitcoin?.brl) { cachedBtcBrl = j.bitcoin.brl; lastFetchAt = now; saveCacheLS(cachedBtcBrl); return cachedBtcBrl; }
      } catch(_) { /* tenta próxima */ }
    }
    // 4) fallback: retorna último cache conhecido (mesmo que antigo) para não quebrar a UI
    if (cachedBtcBrl) return cachedBtcBrl;
    const lsAny = loadCacheLS(365 * 24 * 60 * 60 * 1000); // qualquer idade
    if (lsAny) { cachedBtcBrl = lsAny; lastFetchAt = now; return cachedBtcBrl; }
    return null;
  }
  function toNumberBRL(str){
    if (!str) return 0;
    const cleaned = String(str).replace(/[^0-9]/g,'');
    const v = parseInt(cleaned, 10); // somente reais inteiros
    return isNaN(v) ? 0 : v;
  }
  async function updateBRLConversion(){
    const brlRaw = toNumberBRL(amountInput.value);
    // aplica taxas: menos R$1 e menos 5%
    const afterFixed = Math.max(0, brlRaw - 1);
    const brlNet = afterFixed * 0.95; // menos 5%
    if (document.documentElement.classList.contains('asset-btc')) {
      const btcRow = document.querySelector('.amount-stack .amount-row:last-of-type');
      btcRow?.classList.add('loading');
      const price = await getBtcPriceBRL();
      btcRow?.classList.remove('loading');
      const btc = price ? (brlNet / price) : 0;
      outBtc.value = price ? btc.toFixed(8) : '';
      // calcular taxas em BTC e colocar na nota
      const feesEl = getFeesNote();
      if (feesEl) {
        if (price) {
          const brlFees = brlRaw - brlNet; // valor das taxas em BRL
          const btcFees = brlFees / price;
          feesEl.innerHTML = `Taxa: ${btcFees.toFixed(8)} BTC <span class="fee-expl">(R$1 transação + 5%)</span>`;
        } else {
          feesEl.innerHTML = `Taxa: <span class=\"fee-expl\">(R$1 transação + 5%)</span>`;
        }
      }
    } else if (document.documentElement.classList.contains('asset-depix')) {
      // Para DePix mostramos o valor líquido em DPX (1 BRL = 1 DPX assumido como placeholder)
      if (typeof outDPX !== 'undefined' && outDPX) {
        outDPX.value = brlNet > 0 ? brlNet.toFixed(2) : '';
      }
      const feesEl = getFeesNote();
      if (feesEl) {
        const brlFees = brlRaw - brlNet;
        feesEl.innerHTML = `Taxa: ${brlFees.toFixed(2)} DPX <span class=\"fee-expl\">(R$1 transação + 5%)</span>`;
      }
    } else {
      outBtc.value = '';
    }
  }
  amountInput?.addEventListener('input', () => {
    // Normaliza para múltiplos de R$1 visualmente (sem centavos)
    const onlyDigits = amountInput.value.replace(/[^0-9]/g,'');
    amountInput.value = onlyDigits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    clearTimeout(debounce); debounce = setTimeout(updateBRLConversion, 200);
    try { window.EulenState?.set({ amount_brl: onlyDigits }); } catch(_){}
  });
  amountInputDPX?.addEventListener('input', () => {
    const onlyDigits = amountInputDPX.value.replace(/[^0-9]/g,'');
    amountInputDPX.value = onlyDigits.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    clearTimeout(debounce); debounce = setTimeout(() => {
      const brlRaw = toNumberBRL(amountInputDPX.value);
      const afterFixed = Math.max(0, brlRaw - 1);
      const brlNet = afterFixed * 0.95;
      outDPX.value = brlNet > 0 ? brlNet.toFixed(2) : '';
      const brlFees = brlRaw - brlNet; // total de taxas em BRL (1 real + 5%)
      const feesEl = getFeesNote();
      if (feesEl) {
        feesEl.innerHTML = `Taxa: ${brlFees.toFixed(2)} DPX <span class=\"fee-expl\">(R$1 transação + 5%)</span>`;
      }
      try { window.EulenState?.set({ amount_brl: onlyDigits, amount_out: outDPX.value }); } catch(_){}
      }, 200);
  });
  document.querySelectorAll('.prev-btn').forEach(btn => btn.addEventListener('click', () => {
    // Não resetamos UI ao voltar; renderizamos a partir do estado global
    if (window.EulenState && typeof window.EulenState.render === 'function') window.EulenState.render();
  }));
});
JS;
    wp_add_inline_script('contact-form-script', $convert_js);
});

// Garante a classe no <body> para o preview e renderização server-side
add_filter('body_class', function (array $classes): array {
    $mode = get_theme_mod('cf_theme_mode', 'light');
    $classes[] = 'cf-mode-' . (in_array($mode, ['light', 'dark'], true) ? $mode : 'light');
    return $classes;
});


