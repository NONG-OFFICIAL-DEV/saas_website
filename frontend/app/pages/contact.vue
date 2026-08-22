<template>
  <section class="section-pad contact-page">
    <Container>
      <div class="header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('contact_page.tag') }}</span>
        <h1 class="section-title">{{ t('contact_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('contact_page.sub') }}</p>
      </div>

      <div class="channel-list" data-aos="fade-up">
        <a v-if="footer.email" :href="`mailto:${footer.email}`" class="channel-item">
          <span class="channel-label">
            <Icon name="mdi-email-outline" size="16" />
            {{ t('contact_page.email_label') }}
          </span>
          <span class="channel-value">{{ footer.email }}</span>
        </a>

        <a v-if="telegramHref" :href="telegramHref" target="_blank" rel="noopener" class="channel-item">
          <span class="channel-label">
            <Icon name="mdi-send-outline" size="16" />
            {{ t('contact_page.telegram_label') }}
          </span>
          <span class="channel-value">{{ t('contact_page.telegram_value') }}</span>
        </a>

        <a v-if="footer.phone" :href="`tel:${footer.phone.replace(/\s+/g, '')}`" class="channel-item">
          <span class="channel-label">
            <Icon name="mdi-phone-outline" size="16" />
            {{ t('contact_page.phone_label') }}
          </span>
          <span class="channel-value">{{ footer.phone }}</span>
        </a>

        <div v-if="footer.address" class="channel-item channel-item--static">
          <span class="channel-label">
            <Icon name="mdi-map-marker-outline" size="16" />
            {{ t('contact_page.address_label') }}
          </span>
          <span class="channel-value">{{ footer.address }}</span>
        </div>
      </div>

      <div v-if="footer.socials?.length" class="socials-row" data-aos="fade-up">
        <a
          v-for="social in footer.socials"
          :key="social.name"
          :href="social.href"
          :aria-label="social.name"
          class="social-link"
          target="_blank"
          rel="noopener"
        >
          <SocialIcon :name="social.name.toLowerCase()" />
        </a>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  const { t } = useI18n()
  const siteContentStore = useSiteContentStore()
  const footer = computed(() => siteContentStore.footer)
  const telegramHref = computed(() => footer.value.socials?.find((s) => s.name === 'Telegram')?.href)

  useSeoMeta({
    title: () => `${t('contact_page.title')} · Nexstack`,
    description: () => t('contact_page.sub')
  })

  await useAsyncData('contact-page-footer', async () => {
    await siteContentStore.fetchFooter()
    return true
  })
</script>

<style scoped>
  .contact-page {
    padding-top: 120px;
    padding-bottom: 100px;
  }
  .header {
    max-width: 620px;
    margin: 0 auto 56px;
  }
  .header-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .channel-list {
    max-width: 480px;
    margin: 0 auto;
  }
  .channel-item {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 20px;
    padding: 20px 0;
    text-decoration: none;
    color: var(--foreground);
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 9%, transparent);
    transition: color 0.15s ease;
  }
  .channel-item:first-child {
    padding-top: 0;
  }
  .channel-item:last-child {
    border-bottom: none;
  }
  a.channel-item:hover {
    color: var(--primary);
  }
  .channel-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    font-weight: 600;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    white-space: nowrap;
  }
  .channel-value {
    font-size: 1.05rem;
    font-weight: 800;
    text-align: right;
    overflow-wrap: anywhere;
  }

  @media (max-width: 480px) {
    .channel-item {
      flex-direction: column;
      align-items: flex-start;
      gap: 6px;
    }
    .channel-value {
      text-align: left;
    }
  }

  .socials-row {
    display: flex;
    justify-content: center;
    gap: 22px;
    margin-top: 48px;
  }
  .social-link {
    display: flex;
    color: color-mix(in srgb, var(--foreground) 45%, transparent);
    transition:
      color 0.15s ease,
      transform 0.15s ease;
  }
  .social-link :deep(svg) {
    width: 28px;
    height: 28px;
  }
  .social-link:hover {
    color: var(--primary);
    transform: translateY(-2px);
  }
</style>
