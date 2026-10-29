<template>
  <section class="section-pad">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll v-if="section.title" as="div" class="text-center detail-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-sub detail-sub">{{ section.subtitle }}</p>
      </RevealOnScroll>

      <div class="detail-blocks">
        <div
          v-for="(item, i) in section.items"
          :key="item.id"
          class="detail-block"
          :class="{ 'detail-block--reverse': i % 2 === 1 }"
        >
          <RevealOnScroll as="div" class="detail-media">
            <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="h-full w-full object-cover" />
            <div v-else class="detail-media-placeholder">
              <Icon :name="item.icon || 'mdi-monitor-dashboard'" size="48" />
            </div>
          </RevealOnScroll>

          <RevealOnScroll as="div" class="detail-text" :delay="80">
            <Badge v-if="item.badge" class="detail-badge" :class="`detail-badge--${item.badge}`">
              {{ badgeLabel(item.badge) }}
            </Badge>
            <h3 class="detail-title">{{ item.title }}</h3>
            <p class="detail-desc">{{ item.description }}</p>

            <ul v-if="item.benefits?.length" class="benefit-list">
              <li v-for="b in item.benefits" :key="b" class="benefit-item">
                <Icon name="mdi-check" size="14" class="benefit-check" />
                <span>{{ b }}</span>
              </li>
            </ul>

            <a
              v-if="item.cta_url && item.cta_label"
              :href="item.cta_url"
              class="detail-cta"
              target="_blank"
              rel="noopener"
            >
              {{ item.cta_label }}
              <Icon name="mdi-arrow-right" size="16" />
            </a>
          </RevealOnScroll>
        </div>
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
  .detail-header {
    max-width: 620px;
    margin: 0 auto 44px;
  }
  .detail-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .detail-blocks {
    display: flex;
    flex-direction: column;
    gap: 72px;
    max-width: 1100px;
    margin: 0 auto;
  }

  .detail-block {
    display: grid;
    gap: 40px;
    align-items: center;
    grid-template-columns: 1fr;
  }
  @media (min-width: 900px) {
    .detail-block {
      grid-template-columns: 1fr 1fr;
    }
    .detail-block--reverse .detail-media {
      order: 2;
    }
    .detail-block--reverse .detail-text {
      order: 1;
    }
  }

  .detail-media {
    aspect-ratio: 4 / 3;
    border-radius: var(--radius-2xl);
    overflow: hidden;
    background: linear-gradient(
      160deg,
      color-mix(in srgb, var(--primary) 14%, transparent),
      color-mix(in srgb, var(--primary) 4%, transparent)
    );
    box-shadow: var(--shadow-lg);
  }
  .detail-media-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: color-mix(in srgb, var(--primary) 60%, transparent);
  }

  .detail-text {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }
  .detail-badge {
    align-self: flex-start;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    border: none;
  }
  .detail-badge--popular {
    background: color-mix(in srgb, var(--primary) 14%, transparent);
    color: var(--primary);
  }
  .detail-badge--new {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
  }
  .detail-badge--pro {
    background: color-mix(in srgb, var(--warning) 16%, transparent);
    color: var(--warning);
  }
  .detail-title {
    font-size: var(--text-h3);
    font-weight: var(--text-h3--font-weight);
    letter-spacing: var(--text-h3--letter-spacing);
    line-height: var(--text-h3--line-height);
    margin: 0;
  }
  .detail-desc {
    font-size: 0.92rem;
    line-height: 1.7;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    margin: 0;
  }

  .benefit-list {
    list-style: none;
    padding: 0;
    margin: 6px 0 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .benefit-item {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.86rem;
    font-weight: 600;
  }
  .benefit-check {
    flex-shrink: 0;
    width: 20px;
    height: 20px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 14%, transparent);
    color: var(--primary);
  }

  .detail-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    font-weight: 700;
    font-size: 0.88rem;
    color: var(--primary);
    text-decoration: none;
  }
  .detail-cta:hover {
    text-decoration: underline;
  }
</style>
