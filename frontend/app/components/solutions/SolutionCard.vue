<template>
  <NuxtLink :to="`/solutions/${solution.slug}`" class="solution-card">
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
  </NuxtLink>
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
    border-radius: 20px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: 0 12px 28px color-mix(in srgb, var(--foreground) 6%, transparent);
    text-decoration: none;
    color: var(--foreground);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    overflow: hidden;
  }
  .solution-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px color-mix(in srgb, var(--foreground) 10%, transparent);
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
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
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
    transition: gap 0.15s;
  }
  .solution-card:hover .card-cta {
    gap: 10px;
  }
</style>
