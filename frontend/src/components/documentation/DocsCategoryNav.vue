<template>
  <nav class="cat-nav">
    <div v-for="category in categories" :key="category.id" class="cat-group">
      <router-link :to="firstArticleLink(category)" class="cat-title">
        <v-icon :icon="category.icon || 'mdi-folder-outline'" size="15" />
        {{ category.name }}
      </router-link>

      <router-link
        v-for="article in category.articles"
        :key="article.id"
        :to="`/documentation/${article.slug}`"
        class="cat-article"
        :class="{ active: article.slug === currentSlug }"
      >
        {{ article.title }}
      </router-link>

      <div v-for="child in category.children" :key="child.id" class="cat-subgroup">
        <router-link :to="firstArticleLink(child)" class="cat-title cat-title--sub">
          {{ child.name }}
        </router-link>
        <router-link
          v-for="article in child.articles"
          :key="article.id"
          :to="`/documentation/${article.slug}`"
          class="cat-article"
          :class="{ active: article.slug === currentSlug }"
        >
          {{ article.title }}
        </router-link>
      </div>
    </div>
  </nav>
</template>

<script setup>
  defineProps({
    categories: { type: Array, default: () => [] },
    currentSlug: { type: String, default: '' }
  })

  function firstArticleLink(category) {
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
    color: rgba(var(--v-theme-on-surface), 0.5);
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
    color: rgba(var(--v-theme-on-surface), 0.7);
    text-decoration: none;
    border-left: 2px solid transparent;
  }
  .cat-article:hover {
    color: rgb(var(--v-theme-primary));
  }
  .cat-article.active {
    color: rgb(var(--v-theme-primary));
    font-weight: 700;
    border-left-color: rgb(var(--v-theme-primary));
  }
  .cat-subgroup {
    margin-left: 8px;
  }
</style>
