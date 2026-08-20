<template>
  <div v-if="store.loadingArticle" class="detail-loading">
    <v-container>
      <v-skeleton-loader type="heading, text, paragraph, paragraph" />
    </v-container>
  </div>

  <div v-else-if="!article" class="not-found">
    <v-container class="text-center">
      <v-icon icon="mdi-file-search-outline" size="48" />
      <h2 class="section-title">{{ t('documentation_article.not_found_title') }}</h2>
      <p class="section-sub mx-auto">{{ t('documentation_article.not_found_desc') }}</p>
      <v-btn color="primary" rounded="lg" to="/documentation">{{ t('documentation_article.back_to_docs') }}</v-btn>
    </v-container>
  </div>

  <section v-else class="docs-article-page">
    <v-container fluid class="docs-layout" :class="{ 'docs-layout--no-toc': !headings.length }">
      <!-- ── Left: category nav ── -->
      <aside v-if="mdAndUp" class="docs-sidebar docs-sidebar--left">
        <DocsCategoryNav :categories="store.categories" :current-slug="article.slug" />
      </aside>
      <details v-else class="docs-mobile-collapse">
        <summary>{{ t('documentation_article.in_this_category') }}</summary>
        <DocsCategoryNav :categories="store.categories" :current-slug="article.slug" />
      </details>

      <!-- ── Center: article ── -->
      <div class="docs-main">
        <nav class="breadcrumb" aria-label="Breadcrumb">
          <router-link to="/documentation">{{ t('documentation_home.tag') }}</router-link>
          <template v-if="article.category?.parent">
            <v-icon icon="mdi-chevron-right" size="14" />
            <router-link :to="firstArticleLink(article.category.parent)">{{ article.category.parent.name }}</router-link>
          </template>
          <v-icon icon="mdi-chevron-right" size="14" />
          <router-link :to="firstArticleLink(article.category)">{{ article.category?.name }}</router-link>
          <v-icon icon="mdi-chevron-right" size="14" />
          <span class="breadcrumb-current">{{ article.title }}</span>
        </nav>

        <header class="article-header">
          <h1 class="article-title">{{ article.title }}</h1>
          <p v-if="article.excerpt" class="article-excerpt">{{ article.excerpt }}</p>
        </header>

        <!-- Mobile "on this page" -->
        <details v-if="!mdAndUp && headings.length" class="docs-mobile-collapse docs-mobile-collapse--toc">
          <summary>{{ t('documentation_article.on_this_page') }}</summary>
          <DocsTableOfContents :headings="headings" :active-id="activeHeadingId" @navigate="scrollToHeading" />
        </details>

        <div class="article-content" v-html="sanitizedContent" />

        <div class="feedback">
          <p class="feedback-question">{{ t('documentation_article.was_helpful') }}</p>
          <div v-if="!feedbackGiven" class="feedback-buttons">
            <button class="feedback-btn" @click="giveFeedback(true)">👍 {{ t('documentation_article.yes') }}</button>
            <button class="feedback-btn" @click="giveFeedback(false)">👎 {{ t('documentation_article.no') }}</button>
          </div>
          <p v-else class="feedback-thanks">{{ t('documentation_article.feedback_thanks') }}</p>
        </div>

        <div v-if="article.prev || article.next" class="prev-next">
          <router-link v-if="article.prev" :to="`/documentation/${article.prev.slug}`" class="pn-link pn-link--prev">
            <span class="pn-label">{{ t('documentation_article.previous') }}</span>
            <span class="pn-title"><v-icon icon="mdi-arrow-left" size="15" /> {{ article.prev.title }}</span>
          </router-link>
          <router-link v-if="article.next" :to="`/documentation/${article.next.slug}`" class="pn-link pn-link--next">
            <span class="pn-label">{{ t('documentation_article.next') }}</span>
            <span class="pn-title">{{ article.next.title }} <v-icon icon="mdi-arrow-right" size="15" /></span>
          </router-link>
        </div>

        <div v-if="article.related?.length" class="related">
          <h3 class="related-title">{{ t('documentation_article.related_articles') }}</h3>
          <div class="related-grid">
            <router-link v-for="r in article.related" :key="r.slug" :to="`/documentation/${r.slug}`" class="related-card">
              <strong class="related-name">{{ r.title }}</strong>
              <p class="related-excerpt">{{ r.excerpt }}</p>
            </router-link>
          </div>
        </div>
      </div>

      <!-- ── Right: on this page (desktop only) ── -->
      <aside v-if="mdAndUp && headings.length" class="docs-sidebar docs-sidebar--right">
        <div class="toc-title">{{ t('documentation_article.on_this_page') }}</div>
        <DocsTableOfContents :headings="headings" :active-id="activeHeadingId" @navigate="scrollToHeading" />
      </aside>
    </v-container>
  </section>
</template>

<script setup>
  import { computed, nextTick, onBeforeUnmount, onMounted, ref, watch } from 'vue'
  import { useRoute } from 'vue-router'
  import { useI18n } from 'vue-i18n'
  import { useDisplay } from 'vuetify'
  import DOMPurify from 'dompurify'
  import { isAllowedEmbedSrc } from '@/utils/videoEmbed'
  import { useDocumentationStore } from '@/stores/documentation'
  import DocsCategoryNav from '@/components/documentation/DocsCategoryNav.vue'
  import DocsTableOfContents from '@/components/documentation/DocsTableOfContents.vue'

  const route = useRoute()
  const { t } = useI18n()
  const { mdAndUp } = useDisplay()
  const store = useDocumentationStore()

  const article = computed(() => store.currentArticle)
  const feedbackGiven = ref(false)
  const sanitizedContent = ref('')
  const headings = ref([])
  const activeHeadingId = ref(null)
  let observer = null

  function firstArticleLink(category) {
    const slug = category?.articles?.[0]?.slug
    return slug ? `/documentation/${slug}` : '/documentation'
  }

  /** Injects an id on every h2/h3 (for anchor links + scrollspy) and returns the {text, id, level} list used to build "On this page". Content is admin-authored, not public input — safe to parse with a regex; the result still goes through DOMPurify below before rendering. */
  function extractHeadings(html) {
    const seen = new Map()
    const found = []
    const withIds = html.replace(/<(h2|h3)([^>]*)>([\s\S]*?)<\/\1>/gi, (match, tag, attrs, inner) => {
      const text = inner.replace(/<[^>]+>/g, '').trim()
      let id = text.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-+|-+$)/g, '') || 'section'
      const count = seen.get(id) ?? 0
      seen.set(id, count + 1)
      if (count > 0) id = `${id}-${count + 1}`
      found.push({ id, text, level: Number(tag[1]) })
      return `<${tag}${attrs} id="${id}">${inner}</${tag}>`
    })
    return { html: withIds, headings: found }
  }

  function setupScrollSpy() {
    observer?.disconnect()
    if (!headings.value.length) return
    nextTick(() => {
      const elements = headings.value.map(h => document.getElementById(h.id)).filter(Boolean)
      if (!elements.length) return
      observer = new IntersectionObserver(
        entries => {
          const visible = entries.filter(e => e.isIntersecting)
          if (visible.length) activeHeadingId.value = visible[0].target.id
        },
        { rootMargin: '-90px 0px -70% 0px' }
      )
      elements.forEach(el => observer.observe(el))
    })
  }

  /**
   * DOMPurify's ADD_TAGS/ADD_ATTR below allow <iframe> through structurally
   * (stripped of any event handlers / javascript: URIs like anything else),
   * but that alone doesn't restrict *which* src it can point to. This pass
   * removes any iframe whose src isn't our own generated YouTube/Vimeo embed
   * URL — belt-and-suspenders against a compromised/malicious src ever
   * reaching a real <iframe> on the page, even though today's only writer
   * is the trusted admin editor.
   */
  function stripDisallowedIframes(html) {
    const doc = new DOMParser().parseFromString(html, 'text/html')
    doc.querySelectorAll('iframe').forEach(iframe => {
      if (!isAllowedEmbedSrc(iframe.getAttribute('src'))) {
        ;(iframe.closest('div[data-type="video-embed"]') ?? iframe).remove()
      }
    })
    return doc.body.innerHTML
  }

  function scrollToHeading(id) {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }

  function giveFeedback() {
    feedbackGiven.value = true
  }

  function load(slug) {
    feedbackGiven.value = false
    store.fetchArticleBySlug(slug)
  }

  onMounted(() => {
    store.fetchCategories()
    load(route.params.slug)
  })
  watch(() => route.params.slug, slug => load(slug))
  onBeforeUnmount(() => observer?.disconnect())

  watch(
    article,
    a => {
      if (!a?.content) {
        sanitizedContent.value = ''
        headings.value = []
        return
      }
      const { html, headings: found } = extractHeadings(a.content)
      const purified = DOMPurify.sanitize(html, {
        ADD_TAGS: ['iframe'],
        ADD_ATTR: ['allow', 'allowfullscreen', 'frameborder', 'target']
      })
      sanitizedContent.value = stripDisallowedIframes(purified)
      headings.value = found
      activeHeadingId.value = found[0]?.id ?? null
      nextTick(() => setupScrollSpy())

      document.title = a.seo_title || `${a.title} · Documentation · Nexstack`
      let meta = document.querySelector('meta[name="description"]')
      if (!meta) {
        meta = document.createElement('meta')
        meta.setAttribute('name', 'description')
        document.head.appendChild(meta)
      }
      meta.setAttribute('content', a.seo_description || a.excerpt || '')
    },
    { immediate: true }
  )
</script>

<style scoped>
  .docs-article-page {
    padding-top: 100px;
  }

  .docs-layout {
    display: grid;
    grid-template-columns: 220px minmax(0, 1fr) 200px;
    gap: 40px;
    max-width: 1280px;
    align-items: start;
  }
  .docs-layout--no-toc {
    grid-template-columns: 220px minmax(0, 1fr);
  }
  @media (max-width: 1263px) {
    .docs-layout,
    .docs-layout--no-toc {
      grid-template-columns: minmax(0, 1fr);
    }
  }

  .docs-sidebar {
    position: sticky;
    top: 90px;
    max-height: calc(100vh - 110px);
    overflow-y: auto;
    padding-bottom: 40px;
  }
  .toc-title {
    font-size: 0.76rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(var(--v-theme-on-surface), 0.45);
    margin-bottom: 10px;
  }

  .docs-mobile-collapse {
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.1);
  }
  .docs-mobile-collapse summary {
    font-weight: 700;
    font-size: 0.88rem;
    cursor: pointer;
  }
  .docs-mobile-collapse :deep(.cat-nav),
  .docs-mobile-collapse :deep(.toc) {
    margin-top: 12px;
  }

  .breadcrumb {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 4px;
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
    margin-bottom: 20px;
  }
  .breadcrumb a {
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
  }
  .breadcrumb a:hover {
    color: rgb(var(--v-theme-primary));
  }
  .breadcrumb-current {
    color: rgb(var(--v-theme-on-surface));
    font-weight: 600;
  }

  .article-header {
    margin-bottom: 24px;
  }
  .article-title {
    font-size: clamp(1.6rem, 3.4vw, 2.2rem);
    font-weight: 900;
    letter-spacing: -0.5px;
    margin: 0 0 10px;
  }
  .article-excerpt {
    font-size: 1.02rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    line-height: 1.6;
    margin: 0;
  }

  .article-content {
    max-width: 720px;
    font-size: 0.98rem;
    line-height: 1.75;
    color: rgba(var(--v-theme-on-surface), 0.85);
  }
  .article-content :deep(h2) {
    font-size: 1.3rem;
    font-weight: 800;
    margin: 32px 0 12px;
    scroll-margin-top: 100px;
  }
  .article-content :deep(h3) {
    font-size: 1.1rem;
    font-weight: 800;
    margin: 24px 0 10px;
    scroll-margin-top: 100px;
  }
  .article-content :deep(p) {
    margin: 0 0 14px;
  }
  .article-content :deep(ul),
  .article-content :deep(ol) {
    margin: 0 0 14px;
    padding-left: 24px;
  }
  .article-content :deep(li) {
    margin-bottom: 6px;
  }
  .article-content :deep(blockquote) {
    margin: 0 0 14px;
    padding-left: 16px;
    border-left: 3px solid rgba(var(--v-theme-primary), 0.4);
    color: rgba(var(--v-theme-on-surface), 0.65);
  }
  .article-content :deep(pre) {
    background: rgba(var(--v-theme-on-surface), 0.06);
    border-radius: 10px;
    padding: 14px 16px;
    overflow-x: auto;
    margin: 0 0 14px;
    font-size: 0.86rem;
  }
  .article-content :deep(img) {
    max-width: 100%;
    border-radius: 10px;
    margin: 8px 0;
  }
  .article-content :deep(table) {
    border-collapse: collapse;
    width: 100%;
    margin: 0 0 16px;
    display: block;
    overflow-x: auto;
  }
  .article-content :deep(td),
  .article-content :deep(th) {
    border: 1px solid rgba(var(--v-theme-on-surface), 0.14);
    padding: 8px 12px;
    font-size: 0.9rem;
  }
  .article-content :deep(th) {
    background: rgba(var(--v-theme-on-surface), 0.04);
    text-align: left;
  }
  .article-content :deep(div[data-type='callout']) {
    padding: 14px 18px;
    border-radius: 12px;
    margin: 0 0 16px;
    font-size: 0.92rem;
  }
  .article-content :deep(div[data-type='callout'] p) {
    margin: 0 0 4px;
  }
  .article-content :deep(div[data-type='callout'][data-variant='tip']) {
    background: rgba(99, 102, 241, 0.08);
    border-left: 3px solid rgb(var(--v-theme-primary));
  }
  .article-content :deep(div[data-type='callout'][data-variant='important']) {
    background: rgba(245, 158, 11, 0.1);
    border-left: 3px solid #f59e0b;
  }
  .article-content :deep(div[data-type='callout'][data-variant='note']) {
    background: rgba(var(--v-theme-on-surface), 0.05);
    border-left: 3px solid rgba(var(--v-theme-on-surface), 0.3);
  }

  .article-content :deep(div[data-type='video-embed']) {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    margin: 0 0 20px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 14px 32px rgba(var(--v-theme-on-surface), 0.08);
  }
  .article-content :deep(div[data-type='video-embed'] iframe) {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    border: none;
  }

  .feedback {
    max-width: 720px;
    margin-top: 40px;
    padding: 20px 24px;
    border-radius: 14px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    background: rgba(var(--v-theme-on-surface), 0.02);
  }
  .feedback-question {
    font-weight: 700;
    margin: 0 0 12px;
  }
  .feedback-buttons {
    display: flex;
    gap: 10px;
  }
  .feedback-btn {
    padding: 8px 18px;
    border-radius: 999px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
    background: rgb(var(--v-theme-surface));
    cursor: pointer;
    font-size: 0.86rem;
    font-weight: 600;
  }
  .feedback-btn:hover {
    border-color: rgb(var(--v-theme-primary));
  }
  .feedback-thanks {
    margin: 0;
    color: rgb(var(--v-theme-primary));
    font-weight: 600;
  }

  .prev-next {
    max-width: 720px;
    display: flex;
    justify-content: space-between;
    gap: 16px;
    margin-top: 28px;
    flex-wrap: wrap;
  }
  .pn-link {
    display: flex;
    flex-direction: column;
    gap: 4px;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
    flex: 1;
    min-width: 200px;
  }
  .pn-link--next {
    text-align: right;
    align-items: flex-end;
  }
  .pn-link:hover {
    border-color: rgb(var(--v-theme-primary));
  }
  .pn-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: rgba(var(--v-theme-on-surface), 0.45);
  }
  .pn-title {
    font-weight: 700;
    font-size: 0.92rem;
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .related {
    max-width: 720px;
    margin-top: 40px;
  }
  .related-title {
    font-size: 1.05rem;
    font-weight: 800;
    margin: 0 0 16px;
  }
  .related-grid {
    display: grid;
    gap: 14px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
  }
  .related-card {
    display: block;
    padding: 16px 18px;
    border-radius: 12px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    text-decoration: none;
    color: rgb(var(--v-theme-on-surface));
  }
  .related-card:hover {
    border-color: rgb(var(--v-theme-primary));
  }
  .related-name {
    display: block;
    font-size: 0.92rem;
    margin-bottom: 4px;
  }
  .related-excerpt {
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin: 0;
  }

  .not-found,
  .detail-loading {
    padding: 140px 0 100px;
  }
</style>
