<template>
  <nav class="cat-nav">
    <div v-for="category in categories" :key="category.id" class="cat-group">
      <NuxtLink :to="firstArticleLink(category)" class="cat-title">
        <Icon :name="category.icon || 'mdi-folder-outline'" size="15" />
        {{ category.name }}
      </NuxtLink>

      <NuxtLink
        v-for="article in category.articles"
        :key="article.id"
        :to="`/documentation/${article.slug}`"
        class="cat-article"
        :class="{ active: article.slug === currentSlug }"
      >
        {{ article.title }}
      </NuxtLink>

      <div v-for="child in category.children" :key="child.id" class="cat-subgroup">
        <NuxtLink :to="firstArticleLink(child)" class="cat-title cat-title--sub">
          {{ child.name }}
        </NuxtLink>
        <NuxtLink
          v-for="article in child.articles"
          :key="article.id"
          :to="`/documentation/${article.slug}`"
          class="cat-article"
          :class="{ active: article.slug === currentSlug }"
        >
          {{ article.title }}
        </NuxtLink>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
  import type { DocumentationCategory } from '~/types'

  withDefaults(
    defineProps<{
      categories?: DocumentationCategory[]
      currentSlug?: string
    }>(),
    { categories: () => [], currentSlug: '' }
  )

  function firstArticleLink(category: DocumentationCategory) {
    const slug = category.articles?.[0]?.slug
    return slug ? `/documentation/${slug}` : '/documentation'
  }
</script>

<style scoped>
  .cat-nav {
    display: flex;
    flex-direction: column;
    gap: 18px;
  }
  .cat-group {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .cat-title {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    color: color-mix(in srgb, var(--foreground) 50%, transparent);
    text-decoration: none;
    padding: 4px 0;
  }
  .cat-title--sub {
    margin-top: 8px;
    font-size: 0.72rem;
  }
  .cat-article {
    padding: 6px 0 6px 21px;
    font-size: 0.86rem;
    color: color-mix(in srgb, var(--foreground) 70%, transparent);
    text-decoration: none;
    border-left: 2px solid transparent;
  }
  .cat-article:hover {
    color: var(--primary);
  }
  .cat-article.active {
    color: var(--primary);
    font-weight: 700;
    border-left-color: var(--primary);
  }
  .cat-subgroup {
    margin-left: 8px;
  }
</style>
