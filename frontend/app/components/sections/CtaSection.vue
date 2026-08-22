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
          <Button as="NuxtLink" to="/products" class="cta-btn-solid px-8 font-bold text-primary bg-white">
            {{ t('button.view_products') }}
            <Icon name="mdi-arrow-right" size="18" />
          </Button>
        </div>

        <div class="cta-reassurance">
          <span>{{ t('home.cta.reassurance.no_fees') }}</span>
          <span class="cta-dot" />
          <span>{{ t('home.cta.reassurance.setup') }}</span>
          <span class="cta-dot" />
          <span>{{ t('home.cta.reassurance.cancel_anytime') }}</span>
        </div>

        <div class="contact-grid">
          <a v-if="footer.email" :href="`mailto:${footer.email}`" class="contact-card">
            <Icon name="mdi-email-outline" size="20" />
            <span>{{ footer.email }}</span>
          </a>
          <a v-if="telegramHref" :href="telegramHref" target="_blank" rel="noopener" class="contact-card">
            <Icon name="mdi-send-outline" size="20" />
            <span>{{ t('button.chat_telegram') }}</span>
          </a>
          <a v-if="footer.phone" :href="`tel:${footer.phone.replace(/\s+/g, '')}`" class="contact-card">
            <Icon name="mdi-phone-outline" size="20" />
            <span>{{ footer.phone }}</span>
          </a>
        </div>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'

  const { t } = useI18n()
  const siteContentStore = useSiteContentStore()
  const footer = computed(() => siteContentStore.footer)
  const telegramHref = computed(() => footer.value.socials?.find((s) => s.name === 'Telegram')?.href)

  await useAsyncData('cta-section-footer', async () => {
    await siteContentStore.fetchFooter()
    return true
  })
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
.cta-btn-solid:hover {
  /* !important because Button's own base variant class includes
     [a]:hover:bg-primary/80 — now that this renders as a real <a> tag
     (see Button.vue's as="NuxtLink" fix), that rule and this one have
     identical specificity, so without !important the winner depends on
     unpredictable Tailwind generation order instead of author intent. */
  background-color: rgba(255, 255, 255, 0.8) !important;
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

.contact-grid {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 12px;
  margin-top: 28px;
}
.contact-card {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 20px;
  border-radius: 999px;
  border: 1px solid rgba(255, 255, 255, 0.2);
  background: rgba(255, 255, 255, 0.08);
  color: #fff;
  font-size: 0.86rem;
  font-weight: 600;
  text-decoration: none;
  transition: transform 0.2s ease, background 0.2s ease;
}
.contact-card:hover {
  background: rgba(255, 255, 255, 0.16);
  transform: translateY(-2px);
}

@media (max-width: 640px) {
  .cta-block { padding: 44px 22px; border-radius: 20px; }
  .cta-actions { flex-direction: column; }
  .cta-actions [data-slot='button'] { width: 100%; }
}
</style>
