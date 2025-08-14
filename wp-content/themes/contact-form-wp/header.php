<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php wp_head(); ?>

  <!-- Templates do index.html -->
  <template id="meeting-consent-template">
      <div class="info-box" style="margin-top: 40px; max-width: 500px; margin-left: auto; margin-right: auto;">
          <div style="text-align: center;">
              <p>Para prosseguir, precisaremos agendar uma reunião. É Feita pelo <strong>Meet</strong> (com câmera) e terá duração de <strong>até 30 minutos</strong>.</p>
          </div>
          <div class="checkbox-group" style="display: flex; justify-content: center; align-items: center; margin-top: 20px;">
               <input type="checkbox" id="consent-checkbox" name="meetingConsent" value="true" required>
               <label for="consent-checkbox">Li e concordo com os termos da reunião.</label>
          </div>
          <div class="error-message" id="consent-error" style="text-align: center;">Você precisa concordar com os termos para prosseguir.</div>
      </div>
  </template>

  <template id="user-info-template">
      <div style="max-width: 400px; margin: 0 auto;margin-top:20px;">
          <label for="fullName">Nome/Apelido*</label>
          <input type="text" id="fullName" name="fullName" required>
          <div class="error-message" id="fullName-error">Por favor, digite seu apelido.</div>

          <label for="email">E-mail*</label>
          <input type="email" id="email" name="email" required>
          <div class="error-message" id="email-error">Por favor, digite um e-mail válido.</div>

          <div class="input-with-icon">
              <label for="telegramUser">Telegram*</label>
              <div class="input-icon-group">
                  <span class="input-icon">@</span>
                  <input type="text" id="telegramUser" name="telegramUser" required>
              </div>
              <div class="error-message" id="telegramUser-error">Este campo é obrigatório para contato.</div>
          </div>

          <p class="input-hint">Principal meio de contato.</p>
      </div>
  </template>
</head>
<body <?php body_class(); ?>>

