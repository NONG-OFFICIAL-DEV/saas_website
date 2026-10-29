<template>
  <div v-if="!store.currentPost && store.loadingPost" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!store.currentPost" class="not-found">
    <div class="w-full px-4 max-w-[1280px] mx-auto text-center">
      <Icon name="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('blog_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">{{ t('blog_detail.not_found_desc') }}</p>
      <Button as="NuxtLink" to="/blog">
        {{ t('blog_detail.back_to_blog') }}
      </Button>
    </div>
  </div>

  <template v-else>
    <section class="section-pad hero">
      <div class="w-full px-4 max-w-[1280px] mx-auto">
        <div class="hero-inner">
          <NuxtLinkLocale to="/blog" class="back-link">
            <Icon name="mdi-arrow-left" size="16" /> {{ t('blog_detail.back_to_blog') }}
          </NuxtLinkLocale>
          <div class="post-meta">
            <span v-if="post.author_name">{{ post.author_name }}</span>
            <span v-if="post.author_name && post.published_at">·</span>
            <span v-if="post.published_at">{{ formatDate(post.published_at) }}</span>
          </div>
          <h1 class="hero-title">{{ post.title }}</h1>
          <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" class="cover-image" />
        </div>
      </div>
    </section>

    <section class="section-pad content-section">
      <div class="w-full px-4 max-w-[1280px] mx-auto">
        <div class="post-content">{{ post.content }}</div>
      </div>
    </section>
  </template>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'
  // Explicit import — Vuetify also exports its own `useDate` (date-adapter
  // composable) which Nuxt's auto-import would otherwise resolve instead.
  import { useDate } from '~/composables/useDate'

  const { t } = useI18n()
  const route = useRoute()
  const store = useBlogStore()
  const { formatDate } = useDate()

  const slug = computed(() => String(route.params.slug))
  const post = computed(() => store.currentPost!)

  // Registered before the data fetch below — see products/[slug].vue for
  // why (composable calls after an awaited useAsyncData can lose context).
  useSeoMeta({
    title: () => (store.currentPost ? store.currentPost.seo_title || `${store.currentPost.title} · Nexstack` : undefined),
    description: () => (store.currentPost ? store.currentPost.seo_description || store.currentPost.excerpt || undefined : undefined)
  })

  // Awaited (not onMounted) so this dynamic route's content is present in
  // the server-rendered HTML. `watch: [slug]` re-runs the fetch on
  // client-side navigation between two blog posts.
  await useAsyncData(
    () => `blog-post-${slug.value}`,
    async () => {
      await store.fetchPostBySlug(slug.value)
      return true
    },
    { watch: [slug] }
  )
</script>

<style scoped>
  .hero-inner {
    max-width: 760px;
    margin: 0 auto;
  }
  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.82rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    text-decoration: none;
    margin-bottom: 16px;
  }
  .post-meta {
    display: flex;
    gap: 8px;
    font-size: 0.82rem;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    margin-bottom: 10px;
  }
  .hero-title {
    font-size: var(--text-h1);
    font-weight: var(--text-h1--font-weight);
    letter-spacing: var(--text-h1--letter-spacing);
    line-height: var(--text-h1--line-height);
    margin: 0 0 24px;
  }
  .cover-image {
    width: 100%;
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-lg);
  }

  .content-section {
    padding-top: 0;
  }
  .post-content {
    max-width: 760px;
    margin: 0 auto;
    font-size: 1.02rem;
    line-height: 1.8;
    color: color-mix(in srgb, var(--foreground) 82%, transparent);
    white-space: pre-wrap;
  }

  .not-found,
  .detail-loading {
    padding: 140px 0 100px;
  }
</style>
