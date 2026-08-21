<template>
  <section class="section-pad" id="contact">
    <Container>
      <div class="cta-block text-center" data-aos="zoom-in">
        <h2 class="cta-title text-white mb-3">
          {{ t('home.cta.title') }}
        </h2>
        <p class="cta-sub mb-8">
          {{ t('home.cta.sub') }}
        </p>

        <div class="cta-actions">
          <Button as="NuxtLink" to="/products" class="cta-btn-solid px-8 font-bold text-primary bg-white hover:bg-white/80">
            {{ t('button.view_products') }}
            <Icon name="mdi-arrow-right" size="18" />
          </Button>
          <Button
            as="a"
            variant="outline"
            class="cta-btn-outline px-8 font-bold"
            href="https://t.me/Nong_Phloeut"
            target="_blank"
            rel="noopener"
          >
            <Icon name="mdi-send-outline" size="18" />
            {{ t('button.chat_telegram') }}
          </Button>
        </div>

        <div class="cta-reassurance">
          <span>{{ t('home.cta.reassurance.no_fees') }}</span>
          <span class="cta-dot" />
          <span>{{ t('home.cta.reassurance.setup') }}</span>
          <span class="cta-dot" />
          <span>{{ t('home.cta.reassurance.cancel_anytime') }}</span>
        </div>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'

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
    var(--primary) 0%,
    color-mix(in srgb, var(--primary), black 28%) 100%
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
  box-shadow: 0 8px 24px color-mix(in srgb, var(--foreground) 20%, transparent) !important;
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
  .cta-actions [data-slot='button'] { width: 100%; }
}
</style>
