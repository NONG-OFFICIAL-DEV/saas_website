<template>
  <section class="section-pad">
    <Container class="text-center">
      <div class="mb-10" data-aos="fade-up">
        <span class="section-tag">{{ tag }}</span>
        <h2 class="section-title">{{ title }}</h2>
        <p class="section-sub audience-sub mx-auto">{{ description }}</p>
      </div>

      <div class="audience-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-left">
        <div
          v-for="(ex, i) in examples"
          :key="ex.label"
          data-aos="fade-up"
          :data-aos-delay="i * 80"
          class="audience-card"
          :class="{ 'audience-card--featured': ex.featured, 'audience-card--image': !!ex.image_url }"
        >
          <div v-if="ex.image_url" class="audience-media">
            <img :src="ex.image_url" :alt="ex.label" class="h-full w-full object-cover" />
          </div>
          <div v-else class="audience-icon">
            <Icon :name="ex.icon" size="24" />
          </div>

          <div class="audience-heading">
            <h3 class="audience-title">{{ ex.label }}</h3>
            <Badge v-if="ex.featured" class="audience-badge">{{ t('common.featured') }}</Badge>
          </div>
          <p class="audience-desc">{{ ex.description }}</p>
        </div>
      </div>

      <NuxtLink to="/solutions" class="see-all-link" data-aos="fade-up">
        {{ t('menu.view_all_solutions') }}
        <Icon name="mdi-arrow-right" size="16" />
      </NuxtLink>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Badge } from '~/components/ui/badge'
  import type { AboutAudienceExample } from '~/types'

  defineProps<{
    tag: string
    title: string
    description: string
    examples: AboutAudienceExample[]
  }>()

  const { t } = useI18n()
</script>

<style scoped>
  .audience-sub {
    max-width: 460px;
    margin: 0 auto;
  }

  .audience-grid {
    max-width: 1080px;
    margin: 0 auto;
  }

  .audience-card {
    display: flex;
    flex-direction: column;
    padding: 28px 26px;
    border-radius: 20px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    transition:
      transform 0.2s ease,
      box-shadow 0.2s ease,
      border-color 0.2s ease;
  }
  .audience-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 16px 34px color-mix(in srgb, var(--foreground) 8%, transparent);
    border-color: color-mix(in srgb, var(--primary) 30%, transparent);
  }

  .audience-card--featured {
    background: linear-gradient(
      165deg,
      color-mix(in srgb, var(--primary) 12%, var(--card)) 0%,
      color-mix(in srgb, var(--primary) 5%, var(--card)) 100%
    );
    border: 2px solid color-mix(in srgb, var(--primary) 40%, transparent);
  }

  .audience-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: color-mix(in srgb, var(--primary) 12%, transparent);
    color: var(--primary);
    margin-bottom: 16px;
  }

  .audience-media {
    aspect-ratio: 16 / 9;
    border-radius: 14px;
    overflow: hidden;
    margin: -4px -4px 16px;
    background: linear-gradient(
      160deg,
      color-mix(in srgb, var(--primary) 16%, transparent),
      color-mix(in srgb, var(--primary) 4%, transparent)
    );
  }

  .audience-heading {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
  }
  .audience-title {
    font-size: 1.02rem;
    font-weight: 800;
    margin: 0;
  }
  .audience-badge {
    background: color-mix(in srgb, var(--primary) 14%, transparent);
    color: var(--primary);
    border-color: transparent;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
  }
  .audience-desc {
    font-size: 0.88rem;
    line-height: 1.6;
    color: color-mix(in srgb, var(--foreground) 62%, transparent);
    margin: 0;
  }

  .see-all-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 40px;
    font-size: 0.92rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
  }
  .see-all-link:hover {
    text-decoration: underline;
    text-underline-offset: 3px;
  }
</style>
