<?php get_header(); ?>

<div class="form-container">
  <div class="progress-bar">
    <div class="progress"></div>
  </div>

  <form id="eulen-form">
    <!-- Estado persistente do fluxo -->
    <input type="hidden" name="state_asset" value="">
    <input type="hidden" name="state_category" value="">
    <input type="hidden" name="state_amount_brl" value="">
    <input type="hidden" name="state_amount_out" value="">
    <input type="hidden" name="state_network" value="">
    <input type="hidden" name="state_wallet" value="">
    <input type="hidden" name="state_profileType" value="">
    <!-- STEP 1: Welcome & Category -->
    <div class="form-step active" data-step="1">
      <div class="step-content-wrapper">
        <h2>Bem-vindo à P2P.APP.BR</h2>
        <p class="category-prompt">O que você quer comprar?</p>
        <div style="margin-top: 50px" class="welcome-content">
          <div class="category-selection">
            <div class="radio-group category-options">
              <div class="option-row">
                <input type="radio" id="category-onboarding" name="category" value="onboarding" required autocomplete="off">
                <label for="category-onboarding" class="option-card">
                  <span class="btc-logo--lg" aria-hidden="true">₿</span>
                  <span class="crypto-name">Bitcoin</span>
                </label>
                <input type="radio" id="category-security" name="category" value="security" required autocomplete="off">
                <label for="category-security" class="option-card">
                  <span class="depix-logo--lg" aria-hidden="true">Đ</span>
                  <span class="crypto-name">DePix</span>
                </label>
              </div>
              
              <!-- Step: Amount input (new) -->
              
            </div>
            <div class="error-message" id="category-error">Por favor, selecione uma opção.</div>
          </div>
        </div>
      </div>
      <div class="button-container">
        <!-- Botão Removido -->
      </div>
    </div>

    <!-- STEP 2: Amount (BRL -> BTC) -->
    <div class="form-step" data-step="2" data-kind="amount">
      <div class="step-content-wrapper">
        <h2>Quanto você quer comprar?</h2>
        <div class="step-state-chips"></div>
        <div class="amount-stack">
          <label for="desiredAmountBRL">Você paga:</label>
          <div class="amount-row">
            <span class="prefix currency-brl" id="amountPrefix">R$</span>
            <input id="desiredAmountBRL" type="text" inputmode="decimal" placeholder="0" autocomplete="off" required>
          </div>

          <label for="convertedAmountBTC">Você recebe:</label>
          <div class="amount-row">
            <span class="prefix btc-logo--sm" id="convertedPrefix">₿</span>
            <input id="convertedAmountBTC" type="text" placeholder="0.00000000" readonly disabled tabindex="-1" aria-readonly="true" aria-disabled="true">
            <span class="suffix" id="convertedSuffix">BTC</span>
          </div>
          <div class="amount-fees">Taxas: R$1 (transação) + 5%</div>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style go-wallet-step">Próximo</button>
      </div>
    </div>

    <!-- STEP 2-DEPX: Amount (BRL -> DPX) -->
    <div class="form-step" data-step="2-depix" data-kind="amount-depix">
      <div class="step-content-wrapper">
        <h2>Quanto você quer comprar?</h2>
        <div class="step-state-chips"></div>
        <div class="amount-stack">
          <label for="desiredAmountBRL_DPX">Você paga:</label>
          <div class="amount-row">
            <span class="prefix currency-brl">R$</span>
            <input id="desiredAmountBRL_DPX" type="text" inputmode="decimal" placeholder="0" autocomplete="off" required>
          </div>
          <label for="convertedAmountDPX">Você recebe:</label>
          <div class="amount-row">
            <span class="prefix depix-logo--sm" id="convertedPrefix_DPX">Đ</span>
            <input id="convertedAmountDPX" type="text" placeholder="0" readonly disabled tabindex="-1" aria-readonly="true" aria-disabled="true">
            <span class="suffix" id="convertedSuffix_DPX">DPX</span>
          </div>
          <div class="amount-fees">Taxas: R$1 (transação) + 5%</div>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style go-wallet-step">Próximo</button>
      </div>
    </div>

    <!-- STEP 2.6: Selecionar rede (apenas dois quadrados) -->
    <div class="form-step" data-step="2.6">
      <div class="step-content-wrapper">
        <h2>Para onde enviaremos?</h2>
        <div class="step-state-chips"></div>
        <!-- BLOCO CONFIRM VALORES (comentado a pedido) -->
        <!--
        <div class="amount-stack" id="confirm-amounts">
          <label for="confirmBRL">Você paga:</label>
          <div class="amount-row">
            <span class="prefix currency-brl">R$</span>
            <input id="confirmBRL" type="text" placeholder="0" readonly disabled tabindex="-1" aria-readonly="true" aria-disabled="true">
          </div>
          <label for="confirmOut">Você recebe:</label>
          <div class="amount-row">
            <span class="prefix" id="confirmPrefix">₿</span>
            <input id="confirmOut" type="text" placeholder="0" readonly disabled tabindex="-1" aria-readonly="true" aria-disabled="true">
            <span class="suffix" id="confirmSuffix">BTC</span>
          </div>
          <div class="amount-fees" id="confirmFees">Taxa: <span class="fee-expl">(R$1 transação + 5%)</span></div>
        </div>
        -->

        <div class="welcome-content">
          <div class="radio-group category-options">
            <div class="option-row">
              <input type="radio" id="network-liquid" name="networkChoice" value="liquid" required autocomplete="off">
              <label for="network-liquid" class="option-card">
                <img class="liquid-icon" src="<?php echo esc_url( get_template_directory_uri() ); ?>/assets/Ícones/l-btc.png" alt="Liquid" loading="lazy"/>
                <span class="crypto-name">Liquid</span>
              </label>
              <input type="radio" id="network-lightning" name="networkChoice" value="lightning" required autocomplete="off" disabled>
              <label for="network-lightning" class="option-card option-card--disabled">
                <span class="net-logo net-lightning net-logo--lg" aria-hidden="true">⚡</span>
                <span class="crypto-name">Lightning</span>
                <small class="option-note">em breve</small>
              </label>
            </div>
          </div>
        </div>
        <div class="error-message" id="networkChoice-error">Por favor, selecione uma rede.</div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <!-- STEP 2.7: Endereço da carteira -->
    <div class="form-step" data-step="2.7">
      <div class="step-content-wrapper">
        <h2>Endereço da carteira</h2>
        <div class="step-state-chips"></div>
        <label for="wallet-address">Endereço da carteira*</label>
        <input type="text" id="wallet-address" name="walletAddress" placeholder="bc1..." required autocomplete="off">
        <div class="input-hint" id="wallet-hint"></div>
        <div class="error-message" id="walletAddress-error">Por favor, insira o endereço da sua carteira.</div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style">Próximo</button>
      </div>
    </div>

    <!-- STEP 2.5: General User Info -->
    <div class="form-step" data-step="2.5">
      <div class="step-content-wrapper">
        <div id="user-info-container"></div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style">Próximo</button>
      </div>
    </div>

    <!-- STEP 3: Conditional Flows -->
    <div class="form-step" data-step="3" data-profile="p2p">
      <div class="step-content-wrapper">
        <div id="p2p-meeting-consent"></div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style">Próximo</button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-profile="comercio">
      <div class="step-content-wrapper">
        <div id="comercio-meeting-consent"></div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style">Próximo</button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-profile="plataforma">
      <div class="step-content-wrapper">
        <div id="plataforma-meeting-consent"></div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="button" class="next-btn eulen-button-style">Próximo</button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-profile="usoProprio">
      <div class="step-content-wrapper">
        <div class="info-message" style="margin-top: 50px; margin-bottom: 20px;">
          <h2>Uso Próprio</h2>
          <p style="max-width: 500px; margin-left: auto; margin-right: auto;">Para casos de uso próprio, recomendamos visitar nossa página de parceiros que podem te vender DePix.</p>
        </div>
        <div style="display: flex; align-items: center; justify-content: center; margin-top: 30px;">
          <a href="https://eulen.app/partners-p2p/" class="eulen-button-style" style="text-decoration: none;">Visitar página de parceiros</a>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-category="feature">
      <div class="step-content-wrapper">
        <div class="info-message" style="margin-top: 50px; margin-bottom: 20px;">
          <h2>Sugestão / Feature request</h2>
          <p>Tem uma ideia para melhorar a Eulen ou o DePix?</p>
          <p>Coloque na nossa plataforma de sugestões:</p>
        </div>
        <div style="display: flex; align-items: center; justify-content: center; margin-top: 30px;">
          <a href="https://feedback.eulen.app" target="_blank" class="eulen-button-style" style="text-decoration: none;">Acessar feedback.eulen.app</a>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-category="security">
      <div class="step-content-wrapper">
        <h2>Reportar Problema / Bug Bounty</h2>
        <div class="info-message" style="text-align: center; margin-bottom: 30px;">
          <p style="max-width: 500px; margin: 0 auto;">Nos envie uma descrição detalhada do problema incluindo:</p>
          <ul style="text-align: left; display: inline-block; margin: 15px auto;">
            <li>Tipo de vulnerabilidade (crítica, alta, média, baixa)</li>
            <li>Descrição detalhada do problema encontrado</li>
            <li>Passos para reproduzir</li>
            <li>Impacto potencial</li>
          </ul>
        </div>
        <div style="text-align: center;">
          <span class="eulen-button-style" style="text-decoration: none; display: inline-block; user-select: text !important; -webkit-user-select: text !important; -moz-user-select: text !important; -ms-user-select: text !important; cursor: text !important;">security@depix.info</span>
          <button type="button" onclick="copySecurityEmail()" style="background: transparent; border: none; cursor: pointer; margin-left: 2px; padding: 8px; vertical-align: middle; position: relative;" title="Copiar e-mail">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 4V16C8 17.1 8.9 18 10 18H18C19.1 18 20 17.1 20 16V4C20 2.9 19.1 2 18 2H10C8.9 2 8 2.9 8 4Z" stroke="#e0e0e0" stroke-width="2"/><path d="M16 18V20C16 21.1 15.1 22 14 22H6C4.9 22 4 21.1 4 20V8C4 6.9 4.9 6 6 6H8" stroke="#e0e0e0" stroke-width="2"/></svg>
            <span style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); color: white; padding: 3px 6px; border-radius: 3px; font-size: 10px; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap;" id="securityCopyTooltip">Copiado!</span>
          </button>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-category="general">
      <div class="step-content-wrapper">
        <h2>Pesquise sua dúvida:</h2>
        <div class="search-container">
          <div class="search-field-container">
            <input type="text" id="question-search" name="questionSearch" placeholder="Ex.: Como faço para me tornar parceiro?" class="search-input">
          </div>
          <div id="search-results" class="search-results-container"></div>
        </div>
        <p style="text-align: center; margin-top: 30px; margin-bottom: 10px;">Se não encontrou a resposta, nos envie suas dúvidas em:</p>
        <div style="text-align: center;">
          <a href="https://t.me/m/zQeA4clzZDUx" target="_blank" class="eulen-button-style" style="text-decoration: none; display: inline-flex; align-items: center; margin-top: 10px; user-select: text !important; -webkit-user-select: text !important; -moz-user-select: text !important; -ms-user-select: text !important; cursor: pointer !important;">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/Ícones/icons8-telegram-app.svg" style="width: 20px; height: 20px; margin-right: 8px;" alt="Telegram Icon" draggable="false">
            @suporte_eulen
          </a>
          <button type="button" onclick="copyToClipboard()" style="background: transparent; border: none; cursor: pointer; margin-left: 2px; padding: 8px; vertical-align: middle; position: relative;" title="Copiar usuário">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8 4V16C8 17.1 8.9 18 10 18H18C19.1 18 20 17.1 20 16V4C20 2.9 19.1 2 18 2H10C8.9 2 8 2.9 8 4Z" stroke="#e0e0e0" stroke-width="2"/><path d="M16 18V20C16 21.1 15.1 22 14 22H6C4.9 22 4 21.1 4 20V8C4 6.9 4.9 6 6 6H8" stroke="#e0e0e0" stroke-width="2"/></svg>
            <span style="position: absolute; top: -20px; left: 50%; transform: translateX(-50%); background: rgba(0,0,0,0.7); color: white; padding: 3px 6px; border-radius: 3px; font-size: 10px; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap;" id="copyTooltip">Copiado!</span>
          </button>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-category="jobs">
      <div class="step-content-wrapper">
        <div class="info-message" style="margin-top: 50px; margin-bottom: 20px;">
          <h2>Vagas</h2>
          <p style="max-width: 500px; margin-left: auto; margin-right: auto;">Confira nossas oportunidades abertas e candidate-se diretamente em nossa página de carreiras.</p>
        </div>
        <div style="display: flex; align-items: center; justify-content: center; margin-top: 30px;">
          <a href="https://jobs.eulen.app" target="_blank" class="eulen-button-style" style="text-decoration: none;">Visite jobs.eulen.app</a>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>

    <div class="form-step" data-step="3" data-category="complex">
      <div class="step-content-wrapper">
        <h2>Dúvidas Complexas</h2>
        <label for="complex-topic">Área da dúvida*</label>
        <select id="complex-topic" name="complexTopic" required>
          <option value="">Selecione a área</option>
          <option value="api">Questões técnicas</option>
          <option value="taxas">Custos de operação</option>
          <option value="compliance">Compliance e Regulação</option>
          <option value="security">Segurança e Privacidade</option>
          <option value="other">Outro</option>
        </select>
        <div class="error-message" id="complexTopic-error">Por favor, selecione uma área.</div>
        <label for="complex-description">Detalhes da sua dúvida*</label>
        <textarea id="complex-description" name="complexDescription" rows="5" required placeholder="Descreva sua dúvida complexa detalhadamente"></textarea>
        <div class="error-message" id="complexDescription-error">Por favor, descreva sua dúvida em detalhes.</div>
        <label for="complex-context">Contexto e sistema atual*</label>
        <textarea id="complex-context" name="complexContext" rows="3" required placeholder="Forneça informações sobre seu ambiente/sistema atual e como este questionamento se relaciona ao seu caso"></textarea>
        <div class="error-message" id="complexContext-error">Por favor, forneça o contexto.</div>
        <label for="complex-tried">O que já tentou? (opcional)</label>
        <textarea id="complex-tried" name="complexTried" rows="3" placeholder="Descreva soluções que você já tentou, se aplicável"></textarea>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
        <button type="submit" class="submit-btn eulen-button-style">Enviar</button>
      </div>
    </div>

    <div class="form-step" data-step="4" style="text-align: center;">
      <div class="step-content-wrapper">
        <h2>Pagamento via Pix</h2>
          <div class="step-state-chips"></div>
        <div id="final-message-content" style="margin-bottom: 8px;"></div>
        <div id="pix-payment-container" class="pix-payment pix-payment--compact">
          <div class="qr-box" aria-label="QR Code do Pix">
            <img id="pix-qr-image" alt="" draggable="false" />
          </div>
          <div class="brcode-box">
            <input id="pix-brcode" type="text" readonly>
            <span id="pix-brcode-preview" class="brcode-preview" aria-hidden="true"></span>
            <button type="button" id="pix-copy" class="copy-icon" title="Copiar"></button>
          </div>
          <div id="pix-status" class="pix-status waiting">Aguardando pagamento...</div>
          <button type="button" id="pix-simulate-webhook" class="eulen-button-style" style="display:none; margin-top:10px;">Simular pagamento (webhook)</button>
        </div>
      </div>
      <div class="button-container">
        <button type="button" class="prev-btn">
          <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 19l-7-7 7-7" stroke-linecap="round" stroke-linejoin="round"/></svg>
        </button>
      </div>
    </div>
  </form>
</div>

<?php get_footer(); ?>


