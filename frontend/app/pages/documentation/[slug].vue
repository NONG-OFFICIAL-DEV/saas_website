<template>
  <div v-if="!article && store.loadingArticle" class="detail-loading">
    <InlineLoader min-height="240px" />
  </div>

  <div v-else-if="!article" class="not-found">
    <Container class="text-center">
      <Icon name="mdi-file-search-outline" size="48" />
      <h2 class="section-title">{{ t('documentation_article.not_found_title') }}</h2>
      <p class="section-sub mx-auto">{{ t('documentation_article.not_found_desc') }}</p>
      <Button as="NuxtLink" to="/documentation">{{ t('documentation_article.back_to_docs') }}</Button>
    </Container>
  </div>

  <section v-else class="docs-article-page">
    <Container fluid class="docs-layout" :class="{ 'docs-layout--no-toc': !headings.length }">
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
          <NuxtLink to="/documentation">{{ t('documentation_home.tag') }}</NuxtLink>
          <template v-if="article.category?.parent">
            <Icon name="mdi-chevron-right" size="14" />
            <NuxtLink :to="firstArticleLink(article.category.parent)">{{ article.category.parent.name }}</NuxtLink>
          </template>
          <Icon name="mdi-chevron-right" size="14" />
          <NuxtLink :to="firstArticleLink(article.category)">{{ article.category?.name }}</NuxtLink>
          <Icon name="mdi-chevron-right" size="14" />
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
            <button class="feedback-btn" @click="giveFeedback()">👍 {{ t('documentation_article.yes') }}</button>
            <button class="feedback-btn" @click="giveFeedback()">👎 {{ t('documentation_article.no') }}</button>
          </div>
          <p v-else class="feedback-thanks">{{ t('documentation_article.feedback_thanks') }}</p>
        </div>

        <div v-if="article.prev || article.next" class="prev-next">
          <NuxtLink v-if="article.prev" :to="`/documentation/${article.prev.slug}`" class="pn-link pn-link--prev">
            <span class="pn-label">{{ t('documentation_article.previous') }}</span>
            <span class="pn-title"><Icon name="mdi-arrow-left" size="15" /> {{ article.prev.title }}</span>
          </NuxtLink>
          <NuxtLink v-if="article.next" :to="`/documentation/${article.next.slug}`" class="pn-link pn-link--next">
            <span class="pn-label">{{ t('documentation_article.next') }}</span>
            <span class="pn-title">{{ article.next.title }} <Icon name="mdi-arrow-right" size="15" /></span>
          </NuxtLink>
        </div>

        <div v-if="article.related?.length" class="related">
          <h3 class="related-title">{{ t('documentation_article.related_articles') }}</h3>
          <div class="related-grid">
            <NuxtLink v-for="r in article.related" :key="r.slug" :to="`/documentation/${r.slug}`" class="related-card">
              <strong class="related-name">{{ r.title }}</strong>
              <p class="related-excerpt">{{ r.excerpt }}</p>
            </NuxtLink>
          </div>
        </div>
      </div>

      <!-- ── Right: on this page (desktop only) ── -->
      <aside v-if="mdAndUp && headings.length" class="docs-sidebar docs-sidebar--right">
        <div class="toc-title">{{ t('documentation_article.on_this_page') }}</div>
        <DocsTableOfContents :headings="headings" :active-id="activeHeadingId" @navigate="scrollToHeading" />
      </aside>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'
  import type { DocumentationCategory } from '~/types'

  interface Heading {
    id: string
    text: string
    level: number
  }

  const route = useRoute()
  const { t } = useI18n()
  const store = useDocumentationStore()

  // Vuetify's useDisplay().mdAndUp replacement — same 960px breakpoint.
  // Defaults to true (desktop layout) for the server-rendered HTML, since
  // the real viewport width is unknowable during SSR; updates once
  // mounted, matching Vuetify's own SSR-default-then-client-corrects
  // behavior for this composable.
  const mdAndUp = ref(true)
  let mdQuery: MediaQueryList | null = null
  function onMdChange(e: MediaQueryListEvent) {
    mdAndUp.value = e.matches
  }
  onMounted(() => {
    mdQuery = window.matchMedia('(min-width: 960px)')
    mdAndUp.value = mdQuery.matches
    mdQuery.addEventListener('change', onMdChange)
  })
  onBeforeUnmount(() => mdQuery?.removeEventListener('change', onMdChange))

  const slug = computed(() => String(route.params.slug))
  const article = computed(() => store.currentArticle)
  const feedbackGiven = ref(false)
  const sanitizedContent = ref('')
  const headings = ref<Heading[]>([])
  const activeHeadingId = ref<string | null>(null)
  let observer: IntersectionObserver | null = null

  function firstArticleLink(category?: DocumentationCategory | null) {
    const slug = category?.articles?.[0]?.slug
    return slug ? `/documentation/${slug}` : '/documentation'
  }

  /** Injects an id on every h2/h3 (for anchor links + scrollspy) and returns the {text, id, level} list used to build "On this page". Content is admin-authored, not public input — safe to parse with a regex; the result still goes through DOMPurify below before rendering. */
  function extractHeadings(html: string) {
    const seen = new Map<string, number>()
    const found: Heading[] = []
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
    // Browser-only (IntersectionObserver, document) — never runs during SSR.
    if (import.meta.server) return
    observer?.disconnect()
    if (!headings.value.length) return
    nextTick(() => {
      const elements = headings.value.map((h) => document.getElementById(h.id)).filter(Boolean) as HTMLElement[]
      if (!elements.length) return
      observer = new IntersectionObserver(
        (entries) => {
          const visible = entries.filter((e) => e.isIntersecting)
          if (visible.length) activeHeadingId.value = visible[0]!.target.id
        },
        { rootMargin: '-90px 0px -70% 0px' }
      )
      elements.forEach((el) => observer!.observe(el))
    })
  }

  function scrollToHeading(id: string) {
    document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' })
  }

  function giveFeedback() {
    feedbackGiven.value = true
  }

  // Registered before the data fetches below — see products/[slug].vue for
  // why (composable calls after an awaited useAsyncData can lose context).
  useSeoMeta({
    title: () => (article.value ? article.value.seo_title || `${article.value.title} · Documentation · Nexstack` : undefined),
    description: () => (article.value ? article.value.seo_description || article.value.excerpt || undefined : undefined)
  })

  // Awaited (not onMounted) so this dynamic route's content is present in
  // the server-rendered HTML. Categories (left nav) only need fetching
  // once; the article itself re-fetches on client-side slug navigation.
  await useAsyncData('documentation-categories-for-article', async () => {
    await store.fetchCategories()
    return true
  })
  await useAsyncData(
    () => `documentation-article-${slug.value}`,
    async () => {
      feedbackGiven.value = false
      await store.fetchArticleBySlug(slug.value)
      return true
    },
    { watch: [slug] }
  )

  onBeforeUnmount(() => observer?.disconnect())

  watch(
    article,
    (a) => {
      if (!a?.content) {
        sanitizedContent.value = ''
        headings.value = []
        return
      }
      const { html, headings: found } = extractHeadings(a.content)
      sanitizedContent.value = sanitizeArticleHtml(html)
      headings.value = found
      activeHeadingId.value = found[0]?.id ?? null
      setupScrollSpy()
    },
    { immediate: true }
  )
</script>

<style scoped>
  .docs-article-page {
    padding: 100px 0 96px;
  }

  .docs-layout {
    display: grid;
    grid-template-columns: 220px minmax(0, 1fr) 200px;
    gap: 40px;
    max-width: 1280px;
    margin: 0 auto;
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
    color: color-mix(in srgb, var(--foreground) 45%, transparent);
    margin-bottom: 10px;
  }

  .docs-mobile-collapse {
    margin-bottom: 20px;
    padding: 12px 16px;
    border-radius: 12px;
    border: 1px solid color-mix(in srgb, var(--foreground) 10%, transparent);
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
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    margin-bottom: 20px;
  }
  .breadcrumb a {
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    text-decoration: none;
  }
  .breadcrumb a:hover {
    color: var(--primary);
  }
  .breadcrumb-current {
    color: var(--foreground);
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
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    line-height: 1.6;
    margin: 0;
  }

  .article-content {
    max-width: 720px;
    font-size: 0.98rem;
    line-height: 1.75;
    color: color-mix(in srgb, var(--foreground) 85%, transparent);
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
    border-left: 3px solid color-mix(in srgb, var(--primary) 40%, transparent);
    color: color-mix(in srgb, var(--foreground) 65%, transparent);
  }
  .article-content :deep(pre) {
    background: color-mix(in srgb, var(--foreground) 6%, transparent);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 14%, transparent);
    padding: 8px 12px;
    font-size: 0.9rem;
  }
  .article-content :deep(th) {
    background: color-mix(in srgb, var(--foreground) 4%, transparent);
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
    border-left: 3px solid var(--primary);
  }
  .article-content :deep(div[data-type='callout'][data-variant='important']) {
    background: rgba(245, 158, 11, 0.1);
    border-left: 3px solid #f59e0b;
  }
  .article-content :deep(div[data-type='callout'][data-variant='note']) {
    background: color-mix(in srgb, var(--foreground) 5%, transparent);
    border-left: 3px solid color-mix(in srgb, var(--foreground) 30%, transparent);
  }

  .article-content :deep(div[data-type='video-embed']) {
    position: relative;
    width: 100%;
    padding-top: 56.25%;
    margin: 0 0 20px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 14px 32px color-mix(in srgb, var(--foreground) 8%, transparent);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    background: color-mix(in srgb, var(--foreground) 2%, transparent);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 12%, transparent);
    background: var(--card);
    cursor: pointer;
    font-size: 0.86rem;
    font-weight: 600;
  }
  .feedback-btn:hover {
    border-color: var(--primary);
  }
  .feedback-thanks {
    margin: 0;
    color: var(--primary);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    text-decoration: none;
    color: var(--foreground);
    flex: 1;
    min-width: 200px;
  }
  .pn-link--next {
    text-align: right;
    align-items: flex-end;
  }
  .pn-link:hover {
    border-color: var(--primary);
  }
  .pn-label {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: color-mix(in srgb, var(--foreground) 45%, transparent);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    text-decoration: none;
    color: var(--foreground);
  }
  .related-card:hover {
    border-color: var(--primary);
  }
  .related-name {
    display: block;
    font-size: 0.92rem;
    margin-bottom: 4px;
  }
  .related-excerpt {
    font-size: 0.82rem;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    margin: 0;
  }

  .not-found,
  .detail-loading {
    padding: 140px 0 100px;
  }
</style>
