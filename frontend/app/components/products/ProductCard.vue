<template>
  <NuxtLink
    :to="`/products/${product.slug}`"
    class="product-card"
    :style="{ '--accent': product.accent_color || '#6366F1' }"
  >
    <div class="card-glow" aria-hidden="true" />

    <div class="card-top">
      <div class="logo-wrap">
        <img
          v-if="product.logo_url"
          :src="product.logo_url"
          :alt="product.name"
          class="logo-img"
        />
        <Icon v-else name="mdi-apps" size="28" />
      </div>

      <span class="status-chip" :class="`status-chip--${product.status}`">
        <span v-if="product.status === 'live'" class="status-dot" />
        {{ t(`common.status.${product.status}`) }}
      </span>
    </div>

    <h3 class="product-name">{{ product.name }}</h3>
    <p class="product-tagline">{{ product.tagline || product.summary }}</p>

    <div class="card-cta">
      <span>{{ t('button.learn_more') }}</span>
      <Icon name="mdi-arrow-right" size="16" />
    </div>
  </NuxtLink>
</template>

<script setup lang="ts">
  import type { Product } from '~/types'

  defineProps<{
    product: Product
  }>()

  const { t } = useI18n()
</script>

<style scoped>
  .product-card {
    position: relative;
    display: flex;
    flex-direction: column;
    gap: 14px;
    padding: 30px 28px;
    border-radius: 22px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow:
      0 14px 32px color-mix(in srgb, var(--foreground) 7%, transparent),
      0 2px 8px color-mix(in srgb, var(--foreground) 4%, transparent);
    text-decoration: none;
    color: var(--foreground);
    transition:
      transform 0.25s ease,
      box-shadow 0.25s ease,
      border-color 0.25s ease;
    overflow: hidden;
  }
  .product-card:hover {
    transform: translateY(-6px);
    box-shadow:
      0 22px 48px color-mix(in srgb, var(--foreground) 12%, transparent),
      0 0 0 1px color-mix(in srgb, var(--accent) 35%, transparent);
    border-color: color-mix(in srgb, var(--accent) 45%, transparent);
  }

  .card-glow {
    position: absolute;
    top: -60px;
    right: -60px;
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: var(--accent);
    opacity: 0.14;
    filter: blur(40px);
    pointer-events: none;
    transition: opacity 0.25s ease;
  }
  .product-card:hover .card-glow {
    opacity: 0.22;
  }

  .card-top {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 10px;
  }

  .logo-wrap {
    width: 56px;
    height: 56px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(
      145deg,
      color-mix(in srgb, var(--accent) 22%, transparent),
      color-mix(in srgb, var(--accent) 10%, transparent)
    );
    color: var(--accent);
    flex-shrink: 0;
    transition: transform 0.25s ease;
  }
  .product-card:hover .logo-wrap {
    transform: scale(1.06) rotate(-2deg);
  }
  .logo-img {
    width: 30px;
    height: 30px;
    object-fit: contain;
  }

  .status-chip {
    position: relative;
    z-index: 1;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.65rem;
    letter-spacing: 0.04em;
    padding: 5px 12px;
    border-radius: 999px;
    white-space: nowrap;
  }
  .status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    animation: status-pulse 2s infinite;
  }
  @keyframes status-pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.4; }
  }
  .status-chip--live {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
  }
  .status-chip--beta {
    background: color-mix(in srgb, var(--info) 14%, transparent);
    color: var(--info);
  }
  .status-chip--coming_soon {
    background: color-mix(in srgb, var(--foreground) 8%, transparent);
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
  }

  .product-name {
    position: relative;
    z-index: 1;
    font-size: 1.2rem;
    font-weight: 800;
    letter-spacing: -0.3px;
    margin: 0;
  }

  .product-tagline {
    position: relative;
    z-index: 1;
    font-size: 0.88rem;
    color: color-mix(in srgb, var(--foreground) 62%, transparent);
    line-height: 1.6;
    margin: 0;
    flex-grow: 1;
  }

  .card-cta {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.83rem;
    font-weight: 700;
    color: var(--accent);
    margin-top: 4px;
    transition: gap 0.15s;
  }
  .product-card:hover .card-cta {
    gap: 10px;
  }
</style>
