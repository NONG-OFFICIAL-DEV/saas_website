<template>
  <NuxtLink :to="`/solutions/${solution.slug}`" class="solution-card">
    <div class="card-glow" aria-hidden="true" />

    <div class="icon-wrap">
      <v-icon :icon="solution.icon || 'mdi-apps'" size="26" />
    </div>

    <h3 class="solution-name">{{ solution.name }}</h3>
    <p class="solution-tagline">{{ solution.tagline }}</p>

    <div class="card-cta">
      <span>{{ t('button.learn_more') }}</span>
      <v-icon icon="mdi-arrow-right" size="16" />
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
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    transition: transform 0.25s ease, box-shadow 0.25s ease;
    overflow: hidden;
  }
  .solution-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(var(--v-theme-on-surface), 0.1);
  }

  .card-glow {
    position: absolute;
    top: -50px;
    right: -50px;
    width: 140px;
    height: 140px;
    border-radius: 50%;
    background: rgb(var(--v-theme-primary));
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
    background: rgba(var(--v-theme-primary), 0.12);
    color: rgb(var(--v-theme-primary));
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
    color: rgba(var(--v-theme-on-surface), 0.6);
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
    color: rgb(var(--v-theme-primary));
    margin-top: 4px;
    transition: gap 0.15s;
  }
  .solution-card:hover .card-cta {
    gap: 10px;
  }
</style>
