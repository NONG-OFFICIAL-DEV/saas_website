<template>
  <div class="legal-page">
    <div class="legal-inner">
      <NuxtLink to="/" class="back-link">
        <v-icon icon="mdi-arrow-left" size="16" />
        {{ t('button.back_to_home') }}
      </NuxtLink>

      <div class="legal-header">
        <div class="legal-icon">
          <v-icon icon="mdi-file-document-outline" size="28" />
        </div>
        <h1 class="legal-title">{{ t('common.terms_of_service') }}</h1>
        <p class="legal-date">
          {{ t('legal.last_updated') }}: {{ t('legal.terms.updated') }}
        </p>
      </div>

      <div class="legal-toc">
        <p class="toc-label">{{ t('legal.toc') }}</p>
        <ol class="toc-list">
          <li v-for="(section, i) in termsSections" :key="i">
            <a :href="`#terms-${i}`">{{ t(section.title) }}</a>
          </li>
        </ol>
      </div>

      <div class="legal-body">
        <div
          v-for="(section, i) in termsSections"
          :key="i"
          :id="`terms-${i}`"
          class="legal-section"
        >
          <h2>{{ i + 1 }}. {{ t(section.title) }}</h2>
          <p v-for="(para, j) in section.paragraphs" :key="j">{{ t(para) }}</p>
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
        <NuxtLink to="/privacy" class="link-primary">
          {{ t('button.view_privacy_policy') }} →
        </NuxtLink>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({ layout: false })

  const { t } = useI18n()

  const termsSections = [
    {
      title: 'legal.terms.s1.title',
      paragraphs: ['legal.terms.s1.p1', 'legal.terms.s1.p2']
    },
    {
      title: 'legal.terms.s2.title',
      paragraphs: ['legal.terms.s2.p1', 'legal.terms.s2.p2']
    },
    {
      title: 'legal.terms.s3.title',
      paragraphs: ['legal.terms.s3.p1', 'legal.terms.s3.p2']
    },
    {
      title: 'legal.terms.s4.title',
      paragraphs: ['legal.terms.s4.p1', 'legal.terms.s4.p2']
    },
    {
      title: 'legal.terms.s5.title',
      paragraphs: ['legal.terms.s5.p1', 'legal.terms.s5.p2']
    },
    {
      title: 'legal.terms.s6.title',
      paragraphs: ['legal.terms.s6.p1']
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
    color: rgb(var(--v-theme-primary));
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
    background: rgba(var(--v-theme-primary), 0.08);
    color: rgb(var(--v-theme-primary));
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
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

  /* Table of contents */
  .legal-toc {
    background: rgba(var(--v-theme-on-surface), 0.03);
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
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
    color: rgb(var(--v-theme-primary));
    text-decoration: none;
    font-weight: 600;
  }
  .toc-list li a:hover {
    text-decoration: underline;
  }

  /* Body */
  .legal-section {
    margin-bottom: 40px;
    scroll-margin-top: 24px;
  }
  .legal-section h2 {
    font-size: 1.05rem;
    font-weight: 800;
    margin-bottom: 12px;
    color: rgb(var(--v-theme-on-surface));
  }
  .legal-section p {
    font-size: 0.9rem;
    line-height: 1.85;
    color: rgba(var(--v-theme-on-surface), 0.7);
    margin-bottom: 10px;
  }

  /* Footer */
  .legal-footer {
    margin-top: 60px;
    padding-top: 24px;
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 10px;
    font-size: 0.84rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
  .link-primary {
    color: rgb(var(--v-theme-primary));
    font-weight: 700;
    text-decoration: none;
  }
  .link-primary:hover {
    text-decoration: underline;
  }
</style>
