<template>
  <div class="legal-page">
    <div class="legal-inner">
      <NuxtLink to="/" class="back-link">
        <Icon name="mdi-arrow-left" size="16" />
        {{ t('button.back_to_home') }}
      </NuxtLink>

      <div class="legal-header">
        <div class="legal-icon privacy-icon">
          <Icon name="mdi-shield-lock-outline" size="28" />
        </div>
        <h1 class="legal-title">{{ t('common.privacy_policy') }}</h1>
        <p class="legal-date">
          {{ t('legal.last_updated') }}: {{ t('legal.privacy.updated') }}
        </p>
      </div>

      <div class="legal-toc">
        <p class="toc-label">{{ t('legal.toc') }}</p>
        <ol class="toc-list">
          <li v-for="(section, i) in privacySections" :key="i">
            <a :href="`#privacy-${i}`">{{ t(section.title) }}</a>
          </li>
        </ol>
      </div>

      <div class="legal-body">
        <div
          v-for="(section, i) in privacySections"
          :key="i"
          :id="`privacy-${i}`"
          class="legal-section"
        >
          <h2>{{ i + 1 }}. {{ t(section.title) }}</h2>
          <p v-for="(para, j) in section.paragraphs" :key="j">{{ t(para) }}</p>

          <!-- Bullet list for data types section -->
          <ul v-if="section.bullets" class="legal-bullets">
            <li v-for="(b, k) in section.bullets" :key="k">{{ t(b) }}</li>
          </ul>
        </div>
      </div>

      <div class="legal-footer">
        <p>
          {{ t('legal.questions') }}
          <a
            href="https://t.me/Nong_Phloeut"
            target="_blank"
            rel="noopener"
            class="link-primary"
          >
            support@nextstack.app
          </a>
        </p>
        <NuxtLink to="/terms" class="link-primary">
          {{ t('button.view_terms_of_service') }} →
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({ layout: false })

  const { t } = useI18n()

  const privacySections = [
    {
      title: 'legal.privacy.s1.title',
      paragraphs: ['legal.privacy.s1.p1']
    },
    {
      title: 'legal.privacy.s2.title',
      paragraphs: ['legal.privacy.s2.p1'],
      bullets: [
        'legal.privacy.s2.b1',
        'legal.privacy.s2.b2',
        'legal.privacy.s2.b3',
        'legal.privacy.s2.b4'
      ]
    },
    {
      title: 'legal.privacy.s3.title',
      paragraphs: ['legal.privacy.s3.p1', 'legal.privacy.s3.p2']
    },
    {
      title: 'legal.privacy.s4.title',
      paragraphs: ['legal.privacy.s4.p1']
    },
    {
      title: 'legal.privacy.s5.title',
      paragraphs: ['legal.privacy.s5.p1', 'legal.privacy.s5.p2']
    },
    {
      title: 'legal.privacy.s6.title',
      paragraphs: ['legal.privacy.s6.p1']
    },
    {
      title: 'legal.privacy.s7.title',
      paragraphs: ['legal.privacy.s7.p1']
    }
  ]
</script>

<style scoped>
  .legal-page {
    padding: 60px 24px 80px;
    min-height: 100vh;
  }
  .legal-inner {
    max-width: 700px;
    margin: 0 auto;
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.84rem;
    color: var(--primary);
    text-decoration: none;
    margin-bottom: 40px;
    font-weight: 600;
    opacity: 0.8;
    transition: opacity 0.15s;
  }
  .back-link:hover {
    opacity: 1;
  }

  .legal-header {
    margin-bottom: 36px;
  }
  .legal-icon {
    width: 52px;
    height: 52px;
    border-radius: 14px;
    background: color-mix(in srgb, var(--primary) 8%, transparent);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
  }
  .privacy-icon {
    background: rgba(99, 102, 241, 0.08);
    color: #6366f1;
  }
  .legal-title {
    font-size: clamp(1.6rem, 4vw, 2.2rem);
    font-weight: 900;
    letter-spacing: -1px;
    margin-bottom: 8px;
    line-height: 1.1;
  }
  .legal-date {
    font-size: 0.82rem;
    opacity: 0.42;
    margin: 0;
  }

  .legal-toc {
    background: color-mix(in srgb, var(--foreground) 3%, transparent);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 44px;
  }
  .toc-label {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    opacity: 0.4;
    margin: 0 0 10px;
  }
  .toc-list {
    margin: 0;
    padding-left: 18px;
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
  .toc-list li a {
    font-size: 0.84rem;
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
  }
  .toc-list li a:hover {
    text-decoration: underline;
  }

  .legal-section {
    margin-bottom: 40px;
    scroll-margin-top: 24px;
  }
  .legal-section h2 {
    font-size: 1.05rem;
    font-weight: 800;
    margin-bottom: 12px;
    color: var(--foreground);
  }
  .legal-section p {
    font-size: 0.9rem;
    line-height: 1.85;
    color: color-mix(in srgb, var(--foreground) 70%, transparent);
    margin-bottom: 10px;
  }
  .legal-bullets {
    padding-left: 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    margin-top: 8px;
  }
  .legal-bullets li {
    font-size: 0.88rem;
    line-height: 1.7;
    color: color-mix(in srgb, var(--foreground) 68%, transparent);
  }

  .legal-footer {
    margin-top: 60px;
    padding-top: 24px;
    border-top: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 0.84rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
  }
  .link-primary {
    color: var(--primary);
    font-weight: 700;
    text-decoration: none;
  }
  .link-primary:hover {
    text-decoration: underline;
  }
</style>
