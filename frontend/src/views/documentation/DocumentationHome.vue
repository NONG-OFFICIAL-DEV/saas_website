<template>
  <section class="section-pad docs-home">
    <v-container>
      <div class="header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('documentation_home.tag') }}</span>
        <h1 class="section-title">{{ t('documentation_home.title') }}</h1>
        <p class="section-sub header-sub">{{ t('documentation_home.sub') }}</p>

        <div class="search-box">
          <v-icon icon="mdi-magnify" size="20" />
          <input
            v-model="query"
            type="text"
            class="search-input"
            :placeholder="t('documentation_home.search_placeholder')"
          />
        </div>
      </div>

      <!-- ── Search results ── -->
      <template v-if="query.trim().length >= 2">
        <div class="search-results" data-aos="fade-up">
          <p class="results-heading">{{ t('documentation_home.search_results_for', { query }) }}</p>

          <div v-if="store.searching" class="results-loading">
            <v-progress-circular indeterminate color="primary" size="28" />
          </div>
          <div v-else-if="!store.searchResults.length" class="no-results">
            {{ t('documentation_home.no_results') }}
          </div>
          <router-link
            v-for="result in store.searchResults"
            :key="result.slug"
            :to="`/documentation/${result.slug}`"
            class="result-card"
          >
            <div class="result-meta">
              <span v-if="result.product">{{ result.product }}</span>
              <span v-if="result.product && result.category">·</span>
              <span v-if="result.category">{{ result.category }}</span>
            </div>
            <h3 class="result-title">{{ result.title }}</h3>
            <p class="result-excerpt">{{ result.excerpt }}</p>
          </router-link>
        </div>
      </template>

      <!-- ── Normal home content ── -->
      <template v-else>
        <InlineLoader v-if="store.loading" min-height="180px" />

        <div v-else class="cards-grid" data-aos="fade-up">
          <div v-for="category in productCategories" :key="category.id" class="product-card">
            <div class="product-icon">
              <v-icon :icon="category.icon || 'mdi-apps'" size="26" />
            </div>
            <h3 class="product-name">{{ category.name }}</h3>
            <p class="product-desc">{{ category.description }}</p>
            <router-link :to="firstArticleLink(category)" class="product-cta">
              {{ t('documentation_home.view_documentation') }}
              <v-icon icon="mdi-arrow-right" size="16" />
            </router-link>
            <a
              v-if="category.product?.demo_video_url"
              :href="category.product.demo_video_url"
              target="_blank"
              rel="noopener"
              class="product-video-link"
            >
              <v-icon icon="mdi-play-circle-outline" size="15" /> Watch demo
            </a>
          </div>
        </div>

        <div v-if="gettingStarted" class="getting-started" data-aos="fade-up">
          <span class="section-tag">{{ t('documentation_home.getting_started_tag') }}</span>
          <h2 class="gs-title">{{ t('documentation_home.getting_started_title') }}</h2>
          <ol class="gs-steps">
            <li v-for="(article, idx) in gettingStarted.articles" :key="article.id">
              <router-link :to="`/documentation/${article.slug}`" class="gs-step">
                <span class="gs-num">{{ String(idx + 1).padStart(2, '0') }}</span>
                <span class="gs-text">{{ article.title }}</span>
              </router-link>
            </li>
          </ol>
        </div>

        <div v-if="generalCategories.length" class="all-categories" data-aos="fade-up">
          <h2 class="gs-title">{{ t('documentation_home.browse_all') }}</h2>
          <div class="category-grid">
            <router-link
              v-for="category in generalCategories"
              :key="category.id"
              :to="firstArticleLink(category)"
              class="category-chip"
            >
              <v-icon :icon="category.icon || 'mdi-folder-outline'" size="18" />
              {{ category.name }}
            </router-link>
          </div>
        </div>
      </template>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, onMounted, ref, watch } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useDocumentationStore } from '@/stores/documentation'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const store = useDocumentationStore()
  const query = ref('')

  onMounted(() => store.fetchCategories())

  let debounceTimer = null
  watch(query, value => {
    clearTimeout(debounceTimer)
    debounceTimer = setTimeout(() => store.search(value), 300)
  })

  // Categories tied to a specific product ("Studio", "Smart Store") are the
  // primary entry points; general categories (Getting Started is pulled out
  // separately below, the rest listed under "browse all").
  const productCategories = computed(() => store.categories.filter(c => c.product_id))
  const gettingStarted = computed(() => store.categories.find(c => c.slug === 'getting-started'))
  const generalCategories = computed(() =>
    store.categories.filter(c => !c.product_id && c.slug !== 'getting-started')
  )

  function firstArticleLink(category) {
    const slug = category.articles?.[0]?.slug
    return slug ? `/documentation/${slug}` : '/documentation'
  }
</script>

<style scoped>
  .docs-home {
    padding-top: 120px;
  }
  .header {
    max-width: 640px;
    margin: 0 auto 40px;
  }
  .header-sub {
    max-width: 480px;
    margin: 0 auto 28px;
  }

  .search-box {
    display: flex;
    align-items: center;
    gap: 10px;
    max-width: 560px;
    margin: 0 auto;
    padding: 14px 20px;
    border-radius: 999px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
    background: rgb(var(--v-theme-surface));
    box-shadow: 0 10px 26px rgba(var(--v-theme-on-surface), 0.06);
    color: rgba(var(--v-theme-on-surface), 0.5);
    transition: border-color 0.2s, box-shadow 0.2s;
  }
  .search-box:focus-within {
    border-color: rgb(var(--v-theme-primary));
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.14);
  }
  .search-input {
    flex: 1;
    border: none;
    outline: none;
    background: transparent;
    font-size: 0.95rem;
    color: rgb(var(--v-theme-on-surface));
  }

  .cards-grid {
    display: grid;
    gap: 24px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    max-width: 760px;
    margin: 0 auto 56px;
  }
  .product-card {
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding: 28px 26px;
    border-radius: 20px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    transition: transform 0.22s ease, box-shadow 0.22s ease;
  }
  .product-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 36px rgba(var(--v-theme-on-surface), 0.1);
  }
  .product-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(99, 102, 241, 0.12);
    color: rgb(var(--v-theme-primary));
  }
  .product-name {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 0;
  }
  .product-desc {
    font-size: 0.88rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    line-height: 1.6;
    margin: 0;
    flex-grow: 1;
  }
  .product-cta {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.86rem;
    font-weight: 700;
    color: rgb(var(--v-theme-primary));
    text-decoration: none;
  }
  .product-video-link {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    margin-top: -2px;
    font-size: 0.8rem;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
  }
  .product-video-link:hover {
    color: rgb(var(--v-theme-primary));
  }

  .getting-started {
    max-width: 640px;
    margin: 0 auto 56px;
    text-align: center;
  }
  .gs-title {
    font-size: 1.3rem;
    font-weight: 800;
    margin: 6px 0 22px;
  }
  .gs-steps {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
    text-align: left;
  }
  .gs-step {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 18px;
    border-radius: 14px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    background: rgb(var(--v-theme-surface));
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    transition: border-color 0.2s;
  }
  .gs-step:hover {
    border-color: rgb(var(--v-theme-primary));
  }
  .gs-num {
    font-size: 0.78rem;
    font-weight: 800;
    color: rgb(var(--v-theme-primary));
  }
  .gs-text {
    font-weight: 600;
    font-size: 0.92rem;
  }

  .all-categories {
    max-width: 760px;
    margin: 0 auto;
    text-align: center;
  }
  .category-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 10px;
  }
  .category-chip {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 999px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    font-size: 0.86rem;
    font-weight: 600;
  }
  .category-chip:hover {
    border-color: rgb(var(--v-theme-primary));
    color: rgb(var(--v-theme-primary));
  }

  .search-results {
    max-width: 640px;
    margin: 0 auto;
  }
  .results-heading {
    font-size: 0.9rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
    margin: 0 0 20px;
  }
  .results-loading,
  .no-results {
    display: flex;
    justify-content: center;
    padding: 40px 0;
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
  .result-card {
    display: block;
    padding: 18px 22px;
    margin-bottom: 12px;
    border-radius: 14px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    transition: border-color 0.2s;
  }
  .result-card:hover {
    border-color: rgb(var(--v-theme-primary));
  }
  .result-meta {
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(var(--v-theme-on-surface), 0.45);
    margin-bottom: 4px;
  }
  .result-title {
    font-size: 1rem;
    font-weight: 800;
    margin: 0 0 4px;
  }
  .result-excerpt {
    font-size: 0.86rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin: 0;
  }
</style>
