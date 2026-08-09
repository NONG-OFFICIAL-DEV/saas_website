<template>
  <section class="section-pad" id="contact">
    <v-container>
      <div class="cta-block text-center" data-aos="zoom-in">
        <h2 class="cta-title text-white mb-3">
          {{ t('cta.title') }}
        </h2>
        <p class="cta-sub mb-8">
          {{ t('cta.sub') }}
        </p>

        <div class="cta-actions">
          <v-btn
            color="white"
            variant="flat"
            rounded="lg"
            class="cta-btn-solid px-8 font-weight-bold text-primary"
            append-icon="mdi-arrow-right"
            to="/products"
          >
            {{ t('cta.self.btn') }}
          </v-btn>
          <v-btn
            variant="outlined"
            rounded="lg"
            class="cta-btn-outline px-8 font-weight-bold"
            prepend-icon="mdi-send-outline"
            href="https://t.me/Nong_Phloeut"
            target="_blank"
            rel="noopener"
          >
            {{ t('cta.contact.btn') }}
          </v-btn>
        </div>

        <div class="cta-reassurance">
          <span>{{ t('cta.reassurance.no_fees') }}</span>
          <span class="cta-dot" />
          <span>{{ t('cta.reassurance.setup') }}</span>
          <span class="cta-dot" />
          <span>{{ t('cta.reassurance.cancel_anytime') }}</span>
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()
</script>

<style scoped>
.cta-block {
  position: relative;
  /* Derived from the theme's actual primary token (not a hardcoded hex)
     so this correctly follows light/dark mode instead of looking
     identical in both. */
  background: linear-gradient(
    120deg,
    rgb(var(--v-theme-primary)) 0%,
    color-mix(in srgb, rgb(var(--v-theme-primary)), black 28%) 100%
  );
  border-radius: 24px;
  padding: 56px 32px;
}

.cta-title {
  font-size: clamp(1.5rem, 3.4vw, 2.2rem);
  font-weight: 900;
  letter-spacing: -0.8px;
}
.cta-sub {
  color: rgba(255, 255, 255, 0.75);
  font-size: 1rem;
  line-height: 1.6;
  max-width: 460px;
  margin-left: auto;
  margin-right: auto;
}

.cta-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 14px;
  flex-wrap: wrap;
}

.cta-btn-solid {
  box-shadow: 0 8px 24px rgba(var(--v-theme-on-surface), 0.2) !important;
}
.cta-btn-outline {
  color: #fff !important;
  border-color: rgba(255, 255, 255, 0.45) !important;
}
.cta-btn-outline:hover {
  background: rgba(255, 255, 255, 0.1) !important;
  border-color: rgba(255, 255, 255, 0.75) !important;
}

.cta-reassurance {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 10px;
  flex-wrap: wrap;
  margin-top: 24px;
  font-size: 0.78rem;
  color: rgba(255, 255, 255, 0.55);
}
.cta-dot {
  width: 3px;
  height: 3px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.4);
}

@media (max-width: 640px) {
  .cta-block { padding: 44px 22px; border-radius: 20px; }
  .cta-actions { flex-direction: column; }
  .cta-actions .v-btn { width: 100%; }
}
</style>
