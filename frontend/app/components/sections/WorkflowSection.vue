<template>
  <section class="section-pad section-tint-sky">
    <div class="w-full px-4 max-w-[1280px] mx-auto">
      <RevealOnScroll v-if="section.title" as="div" class="text-center workflow-header">
        <h2 class="section-title">{{ section.title }}</h2>
        <p v-if="section.subtitle" class="section-sub workflow-sub">{{ section.subtitle }}</p>
      </RevealOnScroll>

      <div class="workflow-track">
        <template v-for="(item, i) in section.items" :key="item.id">
          <RevealOnScroll as="div" class="workflow-step" :delay="i * 100">
            <div class="step-icon">
              <Icon :name="item.icon || 'mdi-circle-medium'" size="24" />
            </div>
            <div class="step-media">
              <img v-if="item.image_url" :src="item.image_url" :alt="item.title" class="h-full w-full object-cover" />
            </div>
            <h3 class="step-title">{{ item.title }}</h3>
            <p v-if="item.description" class="step-desc">{{ item.description }}</p>
          </RevealOnScroll>
          <div v-if="i < section.items.length - 1" class="workflow-connector" aria-hidden="true">
            <Icon name="mdi-arrow-right" size="18" class="connector-icon connector-icon--h" />
            <Icon name="mdi-arrow-down" size="18" class="connector-icon connector-icon--v" />
          </div>
        </template>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
  import type { ProductFeatureSection } from '~/types'

  defineProps<{ section: ProductFeatureSection }>()
</script>

<style scoped>
  .workflow-header {
    max-width: 620px;
    margin: 0 auto 44px;
  }
  .workflow-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .workflow-track {
    display: flex;
    flex-direction: column;
    align-items: center;
    max-width: 900px;
    margin: 0 auto;
  }
  @media (min-width: 900px) {
    .workflow-track {
      flex-direction: row;
      align-items: flex-start;
      justify-content: center;
      flex-wrap: wrap;
    }
  }

  .workflow-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    width: 100%;
    max-width: 220px;
    padding: 22px 18px;
    border-radius: var(--radius-xl);
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: var(--shadow-sm);
  }

  .step-icon {
    width: 44px;
    height: 44px;
    border-radius: 999px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
    margin-bottom: 10px;
  }
  .step-media {
    width: 100%;
    aspect-ratio: 4 / 3;
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 10px;
  }
  .step-media:empty {
    display: none;
  }
  .step-title {
    font-size: 0.92rem;
    font-weight: 800;
    margin: 0 0 4px;
  }
  .step-desc {
    font-size: 0.78rem;
    color: color-mix(in srgb, var(--foreground) 58%, transparent);
    line-height: 1.5;
    margin: 0;
  }

  .workflow-connector {
    display: flex;
    align-items: center;
    justify-content: center;
    color: color-mix(in srgb, var(--primary) 45%, transparent);
    padding: 8px 0;
  }
  .connector-icon--h {
    display: none;
  }
  @media (min-width: 900px) {
    .workflow-connector {
      padding: 0 4px;
      align-self: center;
    }
    .connector-icon--h {
      display: block;
    }
    .connector-icon--v {
      display: none;
    }
  }
</style>
