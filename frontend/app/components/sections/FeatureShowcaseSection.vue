<template>
  <section class="section-pad section-tint-mint">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll v-if="section.title" as="div" class="text-center showcase-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-sub showcase-sub">{{ section.subtitle }}</p>
      </RevealOnScroll>

      <!-- ── Feature selector ── -->
      <RevealOnScroll as="div" class="selector-wrap" :delay="80">
        <div class="selector-track" role="tablist">
          <button
            v-for="item in section.items"
            :key="item.id"
            class="selector-btn"
            :class="{ 'selector-btn--active': activeId === item.id }"
            role="tab"
            :aria-selected="activeId === item.id"
            @click="activeId = item.id"
          >
            <Icon v-if="item.icon" :name="item.icon" size="16" />
            {{ item.title }}
          </button>
        </div>
      </RevealOnScroll>

      <!-- ── Active feature panel ── -->
      <Transition name="showcase-fade" mode="out-in">
        <div v-if="active" :key="active.id" class="showcase-panel">
          <div class="showcase-text">
            <Badge v-if="active.badge" class="showcase-badge" :class="`showcase-badge--${active.badge}`">
              {{ badgeLabel(active.badge) }}
            </Badge>
            <h3 class="showcase-title">{{ active.title }}</h3>
            <p class="showcase-desc">{{ active.description }}</p>

            <ul v-if="active.benefits?.length" class="benefit-list">
              <li v-for="b in active.benefits" :key="b" class="benefit-item">
                <Icon name="mdi-check" size="14" class="benefit-check" />
                <span>{{ b }}</span>
              </li>
            </ul>

            <a
              v-if="active.cta_url && active.cta_label"
              :href="active.cta_url"
              class="showcase-cta"
              target="_blank"
              rel="noopener"
            >
              {{ active.cta_label }}
              <Icon name="mdi-arrow-right" size="16" />
            </a>
          </div>

          <div class="showcase-media">
            <img v-if="active.image_url" :src="active.image_url" :alt="active.title" class="h-full w-full object-cover" />
            <div v-else class="showcase-media-placeholder">
              <Icon :name="active.icon || 'mdi-monitor-dashboard'" size="48" />
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </section>
</template>

<script setup lang="ts">
  import { Badge } from '~/components/ui/badge'
  import type { ProductFeatureSection } from '~/types'

  const props = defineProps<{ section: ProductFeatureSection }>()

  const activeId = ref(props.section.items[0]?.id ?? '')
  const active = computed(() => props.section.items.find((i) => i.id === activeId.value) ?? props.section.items[0])

  function badgeLabel(badge: string) {
    return badge === 'popular' ? 'Popular' : badge === 'new' ? 'New' : badge === 'pro' ? 'Pro' : badge
  }
</script>

<style scoped>
  .showcase-header {
    max-width: 620px;
    margin: 0 auto 32px;
  }
  .showcase-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .selector-wrap {
    display: flex;
    justify-content: center;
    margin-bottom: 40px;
    overflow-x: auto;
    -webkit-mask-image: linear-gradient(to right, transparent 0%, black 5%, black 95%, transparent 100%);
    mask-image: linear-gradient(to right, transparent 0%, black 95%, transparent 100%);
  }
  .selector-track {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: color-mix(in srgb, var(--foreground) 5%, transparent);
    border-radius: 999px;
    padding: 5px;
  }
  .selector-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 9px 18px;
    border: none;
    background: transparent;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    cursor: pointer;
    white-space: nowrap;
    transition: background var(--duration-base) var(--ease-out-quart), color var(--duration-base) var(--ease-out-quart), box-shadow var(--duration-base) var(--ease-out-quart);
  }
  .selector-btn--active {
    background: var(--card);
    color: var(--primary);
    box-shadow: var(--shadow-sm);
  }

  .showcase-panel {
    display: grid;
    gap: 40px;
    align-items: center;
    grid-template-columns: 1fr;
    max-width: 1100px;
    margin: 0 auto;
  }
  @media (min-width: 900px) {
    .showcase-panel {
      grid-template-columns: 0.85fr 1.15fr;
    }
  }

  .showcase-text {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }
  .showcase-badge {
    align-self: flex-start;
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    border: none;
  }
  .showcase-badge--popular {
    background: color-mix(in srgb, var(--primary) 14%, transparent);
    color: var(--primary);
  }
  .showcase-badge--new {
    background: color-mix(in srgb, var(--success) 14%, transparent);
    color: var(--success);
  }
  .showcase-badge--pro {
    background: color-mix(in srgb, var(--warning) 16%, transparent);
    color: var(--warning);
  }
  .showcase-title {
    font-size: var(--text-h2);
    font-weight: var(--text-h2--font-weight);
    letter-spacing: var(--text-h2--letter-spacing);
    line-height: var(--text-h2--line-height);
    margin: 0;
  }
  .showcase-desc {
    font-size: 0.95rem;
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
    font-size: 0.88rem;
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

  .showcase-cta {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 6px;
    font-weight: 700;
    font-size: 0.9rem;
    color: var(--primary);
    text-decoration: none;
  }
  .showcase-cta:hover {
    text-decoration: underline;
  }

  .showcase-media {
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
  .showcase-media-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: color-mix(in srgb, var(--primary) 60%, transparent);
  }

  .showcase-fade-enter-active,
  .showcase-fade-leave-active {
    transition: opacity var(--duration-base) var(--ease-out-expo), transform var(--duration-base) var(--ease-out-expo);
  }
  .showcase-fade-enter-from {
    opacity: 0;
    transform: translateY(10px);
  }
  .showcase-fade-leave-to {
    opacity: 0;
    transform: translateY(-10px);
  }
</style>
