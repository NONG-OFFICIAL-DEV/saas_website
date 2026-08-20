<template>
  <div v-if="store.loadingPost" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!store.currentPost" class="not-found">
    <v-container class="text-center">
      <v-icon icon="mdi-compass-off-outline" size="48" />
      <h2 class="section-title">{{ t('blog_detail.not_found_title') }}</h2>
      <p class="section-sub mx-auto">{{ t('blog_detail.not_found_desc') }}</p>
      <v-btn color="primary" rounded="lg" to="/blog">
        {{ t('blog_detail.back_to_blog') }}
      </v-btn>
    </v-container>
  </div>

  <template v-else>
    <section class="section-pad hero">
      <v-container>
        <div class="hero-inner">
          <router-link to="/blog" class="back-link">
            <v-icon icon="mdi-arrow-left" size="16" /> {{ t('blog_detail.back_to_blog') }}
          </router-link>
          <div class="post-meta">
            <span v-if="post.author_name">{{ post.author_name }}</span>
            <span v-if="post.author_name && post.published_at">·</span>
            <span v-if="post.published_at">{{ formatDate(post.published_at) }}</span>
          </div>
          <h1 class="hero-title">{{ post.title }}</h1>
          <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" class="cover-image" />
        </div>
      </v-container>
    </section>

    <section class="section-pad content-section">
      <v-container>
        <div class="post-content">{{ post.content }}</div>
      </v-container>
    </section>
  </template>
</template>

<script setup>
  import { computed, onMounted, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import { useBlogStore } from '@/stores/blog'
  import { useDate } from '@/composables/useDate'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const route = useRoute()
  const store = useBlogStore()
  const { formatDate } = useDate()

  const post = computed(() => store.currentPost)

  function load(slug) {
    store.fetchPostBySlug(slug)
  }

  onMounted(() => load(route.params.slug))
  watch(() => route.params.slug, slug => load(slug))

  watch(
    post,
    p => {
      if (!p) return
      document.title = p.seo_title || `${p.title} · Nexstack`
      let meta = document.querySelector('meta[name="description"]')
      if (!meta) {
        meta = document.createElement('meta')
        meta.setAttribute('name', 'description')
        document.head.appendChild(meta)
      }
      meta.setAttribute('content', p.seo_description || p.excerpt || '')
    },
    { immediate: true }
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
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
    margin-bottom: 16px;
  }
  .post-meta {
    display: flex;
    gap: 8px;
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
    margin-bottom: 10px;
  }
  .hero-title {
    font-size: clamp(1.7rem, 4vw, 2.6rem);
    font-weight: 900;
    letter-spacing: -1px;
    margin: 0 0 24px;
  }
  .cover-image {
    width: 100%;
    border-radius: 18px;
    box-shadow: 0 18px 40px rgba(var(--v-theme-on-surface), 0.1);
  }

  .content-section {
    padding-top: 0;
  }
  .post-content {
    max-width: 760px;
    margin: 0 auto;
    font-size: 1.02rem;
    line-height: 1.8;
    color: rgba(var(--v-theme-on-surface), 0.82);
    white-space: pre-wrap;
  }

  .not-found,
  .detail-loading {
    padding: 140px 0 100px;
  }
</style>
