<template>
  <NuxtLinkLocale :to="`/solutions/${solution.slug}`" class="solution-card">
    <div class="card-glow" aria-hidden="true" />

    <div class="icon-wrap">
      <Icon :name="solution.icon || 'mdi-apps'" size="26" />
    </div>

    <h3 class="solution-name">{{ solution.name }}</h3>
    <p class="solution-tagline">{{ solution.tagline }}</p>

    <div class="card-cta">
      <span>{{ t('button.learn_more') }}</span>
      <Icon name="mdi-arrow-right" size="16" />
    </div>
  </NuxtLinkLocale>
</template>

<script setup lang="ts">
  import type { Solution } from '~/types'

  defineProps<{
    solution: Solution
  }>()

  const { t } = useI18n()
</script>

<style scoped>
  .solution-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 12px;
    padding: 28px 26px;
    border-radius: var(--radius-2xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: var(--shadow-sm), var(--surface-sheen);
    text-decoration: none;
    color: var(--foreground);
    transition: transform var(--duration-base) var(--ease-out-quart), box-shadow var(--duration-base) var(--ease-out-quart);
    overflow: hidden;
  }
  .solution-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-lg), var(--surface-sheen);
  }

  .card-glow {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: var(--primary);
    opacity: 0.1;
    filter: blur(36px);
    pointer-events: none;
  }

  .icon-wrap {
    position: relative;
    z-index: 1;
    width: 48px;
    height: 48px;
    border-radius: var(--radius-md);
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
    transition: transform var(--duration-base) var(--ease-spring);
  }
  .solution-card:hover .icon-wrap {
    transform: scale(1.08) rotate(-2deg);
  }

  .solution-name {
    position: relative;
    z-index: 1;
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
  }
  .solution-tagline {
    position: relative;
    z-index: 1;
    font-size: 0.86rem;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    line-height: 1.55;
    margin: 0;
    flex-grow: 1;
  }

  .card-cta {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary);
    margin-top: 4px;
    transition: gap var(--duration-fast) var(--ease-out-quart);
  }
  .solution-card:hover .card-cta {
    gap: 10px;
  }
</style>
