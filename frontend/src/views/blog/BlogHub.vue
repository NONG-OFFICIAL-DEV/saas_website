<template>
  <section class="section-pad">
    <v-container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('blog_hub.tag') }}</span>
        <h1 class="section-title">{{ t('blog_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('blog_hub.sub') }}</p>
      </div>

      <InlineLoader v-if="store.loading" min-height="260px" />

      <div v-else-if="store.posts.length" class="hub-grid" data-aos="fade-up">
        <router-link
          v-for="post in store.posts"
          :key="post.id"
          :to="`/blog/${post.slug}`"
          class="post-card"
        >
          <img v-if="post.cover_image_url" :src="post.cover_image_url" :alt="post.title" class="post-cover" />
          <div class="post-body">
            <div class="post-date">{{ formatDate(post.published_at) }}</div>
            <h3 class="post-title">{{ post.title }}</h3>
            <p class="post-excerpt">{{ post.excerpt }}</p>
            <div class="post-cta">
              {{ t('button.learn_more') }}
              <v-icon icon="mdi-arrow-right" size="16" />
            </div>
          </div>
        </router-link>
      </div>

      <div v-else class="empty-state">
        <v-icon icon="mdi-newspaper-variant-outline" size="40" />
        <p>{{ t('blog_hub.empty') }}</p>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useBlogStore } from '@/stores/blog'
  import { useDate } from '@/composables/useDate'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const store = useBlogStore()
  const { formatDate } = useDate()

  onMounted(() => store.fetchPosts())
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
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    overflow: hidden;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
  }
  .post-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 44px rgba(var(--v-theme-on-surface), 0.1);
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
    color: rgba(var(--v-theme-on-surface), 0.45);
  }
  .post-title {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0;
  }
  .post-excerpt {
    font-size: 0.86rem;
    color: rgba(var(--v-theme-on-surface), 0.62);
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
    color: rgb(var(--v-theme-primary));
    margin-top: 6px;
  }

  .empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    padding: 64px 0;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
</style>
