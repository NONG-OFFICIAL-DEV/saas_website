<template>
  <section class="section-pad contact-page">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll as="div" class="header text-center">
        <span class="section-tag">{{ t('contact_page.tag') }}</span>
        <h1 class="section-title">{{ t('contact_page.title') }}</h1>
        <p class="section-sub header-sub">{{ t('contact_page.sub') }}</p>
      </RevealOnScroll>

      <div class="contact-grid">
        <RevealOnScroll as="div" class="form-card clay-surface" :delay="80">
          <h2 class="form-title">{{ t('contact_page.form_title') }}</h2>
          <p class="form-sub">{{ t('contact_page.form_sub') }}</p>

          <form class="contact-form" novalidate @submit.prevent="handleSubmit">
            <div class="field">
              <Label for="contact-name">{{ t('contact_page.form_name') }}</Label>
              <Input
                id="contact-name"
                v-model="form.name"
                :placeholder="t('contact_page.form_name_placeholder')"
                :class="nameError ? 'border-destructive' : ''"
                :aria-invalid="!!nameError"
                :aria-describedby="nameError ? 'contact-name-error' : undefined"
                @update:model-value="nameError = null"
              />
              <p v-if="nameError" id="contact-name-error" class="field-error">{{ nameError }}</p>
            </div>

            <div class="field">
              <Label for="contact-email">{{ t('contact_page.form_email') }}</Label>
              <Input
                id="contact-email"
                v-model="form.email"
                type="email"
                :placeholder="t('contact_page.form_email_placeholder')"
                :class="emailError ? 'border-destructive' : ''"
                :aria-invalid="!!emailError"
                :aria-describedby="emailError ? 'contact-email-error' : undefined"
                @update:model-value="emailError = null"
              />
              <p v-if="emailError" id="contact-email-error" class="field-error">{{ emailError }}</p>
            </div>

            <div class="field">
              <Label for="contact-message">{{ t('contact_page.form_message') }}</Label>
              <Textarea
                id="contact-message"
                v-model="form.message"
                rows="4"
                :placeholder="t('contact_page.form_message_placeholder')"
                :class="messageError ? 'border-destructive' : ''"
                :aria-invalid="!!messageError"
                :aria-describedby="messageError ? 'contact-message-error' : undefined"
                @update:model-value="messageError = null"
              />
              <p v-if="messageError" id="contact-message-error" class="field-error">{{ messageError }}</p>
            </div>

            <Button type="submit" size="lg" class="w-full">
              {{ t('contact_page.form_submit') }}
              <Icon name="mdi-arrow-right" size="18" />
            </Button>
          </form>
        </RevealOnScroll>

        <RevealOnScroll as="div" class="channels-col" :delay="160">
          <div class="channel-list">
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

          <div v-if="footer.socials?.length" class="socials-row">
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
        </RevealOnScroll>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'
  import { Textarea } from '~/components/ui/textarea'

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

  // There is no backend endpoint to submit a contact inquiry to (see
  // frontend/CLAUDE.md — this site deliberately has no platform-level
  // accounts or generic submission API). Rather than faking a "message
  // sent" state that silently does nothing, this opens the visitor's own
  // email client with the message pre-filled to the real support address —
  // genuinely functional, not a dead end.
  const form = reactive({ name: '', email: '', message: '' })
  const nameError = ref<string | null>(null)
  const emailError = ref<string | null>(null)
  const messageError = ref<string | null>(null)

  function validate() {
    nameError.value = form.name.trim() ? null : t('contact_page.form_error_name')
    emailError.value = /.+@.+\..+/.test(form.email) ? null : t('contact_page.form_error_email')
    messageError.value = form.message.trim() ? null : t('contact_page.form_error_message')
    return !nameError.value && !emailError.value && !messageError.value
  }

  function handleSubmit() {
    if (!validate()) return
    if (!footer.value.email) return

    const subject = `Message from ${form.name} via nexstack.app`
    const body = `${form.message}\n\n— ${form.name} (${form.email})`
    window.location.href = `mailto:${footer.value.email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`
  }
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

  .contact-grid {
    display: grid;
    gap: 40px;
    max-width: 920px;
    margin: 0 auto;
    grid-template-columns: 1.2fr 1fr;
    align-items: start;
  }
  @media (max-width: 760px) {
    .contact-grid {
      grid-template-columns: 1fr;
    }
  }

  .form-card {
    padding: 32px;
  }
  .form-title {
    font-size: 1.15rem;
    font-weight: 800;
    margin: 0 0 6px;
  }
  .form-sub {
    font-size: 0.82rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    margin: 0 0 24px;
  }
  .contact-form {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .field-error {
    font-size: 0.75rem;
    color: var(--destructive);
    margin: 0;
  }

  .channels-col {
    padding-top: 4px;
  }

  .channel-list {
    max-width: 480px;
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
    transition: color var(--duration-fast) var(--ease-out-quart);
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
      color var(--duration-base) var(--ease-out-quart),
      transform var(--duration-base) var(--ease-out-quart);
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
