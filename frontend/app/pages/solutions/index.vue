<template>
  <section class="section-pad">
    <Container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('solutions_hub.tag') }}</span>
        <h1 class="section-title">{{ t('solutions_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('solutions_hub.sub') }}</p>
      </div>

      <InlineLoader v-if="store.loading" min-height="200px" />

      <div v-else-if="store.solutions.length" class="hub-grid" data-aos="fade-up">
        <SolutionCard v-for="solution in store.solutions" :key="solution.id" :solution="solution" />
      </div>

      <Alert v-else-if="store.error" variant="destructive" class="mt-6">
        <AlertDescription>{{ store.error }}</AlertDescription>
      </Alert>

      <div v-else class="empty-state">
        <Icon name="mdi-lightbulb-outline" size="40" />
        <p>{{ t('solutions_hub.empty') }}</p>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'

  const { t } = useI18n()
  const store = useSolutionsStore()

  // Awaited (not onMounted) — this is the whole page's content, so it must
  // be present in the server-rendered HTML, not just after hydration.
  await useAsyncData('solutions-hub', async () => {
    await store.fetchSolutions()
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
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
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
