<template>
  <nav class="toc">
    <a
      v-for="heading in headings"
      :key="heading.id"
      :href="`#${heading.id}`"
      class="toc-link"
      :class="[`toc-level-${heading.level}`, { active: heading.id === activeId }]"
      @click.prevent="emit('navigate', heading.id)"
    >
      {{ heading.text }}
    </a>
  </nav>
</template>

<script setup lang="ts">
  interface Heading {
    id: string
    text: string
    level: number
  }

  withDefaults(
    defineProps<{
      headings?: Heading[]
      activeId?: string | null
    }>(),
    { headings: () => [], activeId: null }
  )
  const emit = defineEmits<{ navigate: [id: string] }>()
</script>

<style scoped>
  .toc {
    display: flex;
    flex-direction: column;
    gap: 2px;
  }
  .toc-link {
    padding: 5px 0 5px 12px;
    font-size: 0.82rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    text-decoration: none;
    border-left: 2px solid transparent;
  }
  .toc-link:hover {
    color: rgb(var(--v-theme-primary));
  }
  .toc-link.active {
    color: rgb(var(--v-theme-primary));
    font-weight: 700;
    border-left-color: rgb(var(--v-theme-primary));
  }
  .toc-level-3 {
    padding-left: 22px;
    font-size: 0.78rem;
  }
</style>
