<template>
  <section class="section-pad">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll v-if="section.title" as="div" class="text-center grid-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-sub grid-sub">{{ section.subtitle }}</p>
      </RevealOnScroll>

      <div class="feature-grid">
        <RevealOnScroll
          v-for="(item, i) in section.items"
          :key="item.id"
          :delay="(i % 4) * 80"
        >
          <TiltCard :max-tilt="5" :glare="false">
            <div class="feature-card">
              <div v-if="item.image_url" class="feature-media">
                <img :src="item.image_url" :alt="item.title" class="h-full w-full object-cover" />
              </div>
              <div class="feature-top">
                <div class="feature-icon">
                  <Icon :name="item.icon || 'mdi-check-circle-outline'" size="22" />
                </div>
                <Badge v-if="item.badge" class="feature-badge" :class="`feature-badge--${item.badge}`">
                  {{ badgeLabel(item.badge) }}
                </Badge>
              </div>
              <h3 class="feature-title">{{ item.title }}</h3>
              <p class="feature-desc">{{ item.description }}</p>
              <a v-if="item.cta_url && item.cta_label" :href="item.cta_url" class="feature-cta" target="_blank" rel="noopener">
                {{ item.cta_label }}
                <Icon name="mdi-arrow-right" size="14" />
              </a>
            </div>
          </TiltCard>
        </RevealOnScroll>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Badge } from '~/components/ui/badge'
  import type { ProductFeatureSection } from '~/types'

  defineProps<{ section: ProductFeatureSection }>()

  function badgeLabel(badge: string) {
    return badge === 'popular' ? 'Popular' : badge === 'new' ? 'New' : badge === 'pro' ? 'Pro' : badge
  }
</script>

<style scoped>
  .grid-header {
    max-width: 620px;
    margin: 0 auto 44px;
  }
  .grid-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .feature-grid {
    display: grid;
    gap: 22px;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    max-width: 1120px;
    margin: 0 auto;
  }

  .feature-card {
    position: relative;
    display: flex;
    flex-direction: column;
    height: 100%;
    padding: 26px 24px;
    border-radius: var(--radius-xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: var(--shadow-sm);
    transition:
      box-shadow var(--duration-base) var(--ease-out-quart),
      border-color var(--duration-base) var(--ease-out-quart);
  }
  .feature-card:hover {
    box-shadow: var(--shadow-md);
    border-color: color-mix(in srgb, var(--primary) 25%, transparent);
  }

  .feature-media {
    aspect-ratio: 16 / 10;
    border-radius: 14px;
    overflow: hidden;
    margin: -4px -4px 16px;
    background: color-mix(in srgb, var(--primary) 8%, transparent);
  }

  .feature-top {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 14px;
  }
  .feature-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
  }
  .feature-badge {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    border: none;
  }
  .feature-badge--popular {
    background: color-mix(in srgb, var(--primary) 14%, transparent);
    color: var(--primary);
  }
  .feature-badge--new {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
  }
  .feature-badge--pro {
    background: color-mix(in srgb, var(--warning) 16%, transparent);
    color: var(--warning);
  }

  .feature-title {
    font-size: 1.02rem;
    font-weight: 800;
    margin: 0 0 6px;
  }
  .feature-desc {
    font-size: 0.86rem;
    line-height: 1.6;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    margin: 0;
    flex-grow: 1;
  }
  .feature-cta {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    margin-top: 14px;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
  }
  .feature-cta:hover {
    text-decoration: underline;
  }
</style>
