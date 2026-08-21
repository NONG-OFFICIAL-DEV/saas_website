<template>
  <section class="section-pad">
    <Container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('blog_hub.tag') }}</span>
        <h1 class="section-title">{{ t('blog_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('blog_hub.sub') }}</p>
      </div>

      <InlineLoader v-if="store.loading" min-height="260px" />

      <div v-else-if="store.posts.length" class="hub-grid" data-aos="fade-up">
        <NuxtLink
          v-for="post in store.posts"
          :key="post.id"
          :to="`/blog/${post.slug}`"
          class="post-card"
        >
          <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" class="post-cover" />
          <div class="post-body">
            <div class="post-date">{{ formatDate(post.published_at ?? undefined) }}</div>
            <h3 class="post-title">{{ post.title }}</h3>
            <p class="post-excerpt">{{ post.excerpt }}</p>
            <div class="post-cta">
              {{ t('button.learn_more') }}
              <Icon name="mdi-arrow-right" size="16" />
            </div>
          </div>
        </NuxtLink>
      </div>

      <div v-else class="empty-state">
        <Icon name="mdi-newspaper-variant-outline" size="40" />
        <p>{{ t('blog_hub.empty') }}</p>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  // Explicit import — Vuetify also exports its own `useDate` (date-adapter
  // composable) which Nuxt's auto-import would otherwise resolve instead.
  import { useDate } from '~/composables/useDate'

  const { t } = useI18n()
  const store = useBlogStore()
  const { formatDate } = useDate()

  // Awaited (not onMounted) — this is the whole page's content, so it must
  // be present in the server-rendered HTML, not just after hydration.
  await useAsyncData('blog-hub', async () => {
    await store.fetchPosts()
    return true
  })
</script>

<style scoped>
  .hub-header {
    max-width: 620px;
    margin: 0 auto 48px;
  }
  .hub-sub {
    max-width: 480px;
    margin: 0 auto;
  }

  .hub-grid {
    display: grid;
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  }

  .post-card {
    display: flex;
    flex-direction: column;
    border-radius: 20px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    box-shadow: 0 12px 28px color-mix(in srgb, var(--foreground) 6%, transparent);
    text-decoration: none;
    color: var(--foreground);
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 44px color-mix(in srgb, var(--foreground) 10%, transparent);
  }

  .post-cover {
    width: 100%;
    height: 160px;
    object-fit: cover;
  }

  .post-body {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 22px 24px;
    flex-grow: 1;
  }
  .post-date {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: color-mix(in srgb, var(--foreground) 45%, transparent);
  }
  .post-title {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0;
  }
  .post-excerpt {
    font-size: 0.86rem;
    color: color-mix(in srgb, var(--foreground) 62%, transparent);
    line-height: 1.6;
    margin: 0;
    flex-grow: 1;
  }
  .post-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary);
    margin-top: 6px;
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 64px 0;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
  }
</style>
