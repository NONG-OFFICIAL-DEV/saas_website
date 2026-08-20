<template>
  <section class="section-pad">
    <v-container>
      <div class="hub-header text-center" data-aos="fade-up">
        <span class="section-tag">{{ t('solutions_hub.tag') }}</span>
        <h1 class="section-title">{{ t('solutions_hub.title') }}</h1>
        <p class="section-sub hub-sub">{{ t('solutions_hub.sub') }}</p>
      </div>

      <InlineLoader v-if="store.loading" min-height="200px" />

      <div v-else-if="store.solutions.length" class="hub-grid" data-aos="fade-up">
        <SolutionCard v-for="solution in store.solutions" :key="solution.id" :solution="solution" />
      </div>

      <v-alert v-else-if="store.error" type="error" variant="tonal" rounded="lg" class="mt-6">
        {{ store.error }}
      </v-alert>

      <div v-else class="empty-state">
        <v-icon icon="mdi-lightbulb-outline" size="40" />
        <p>{{ t('solutions_hub.empty') }}</p>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { onMounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useSolutionsStore } from '@/stores/solutions'
  import SolutionCard from '@/components/solutions/SolutionCard.vue'
  import InlineLoader from '@/components/global/InlineLoader.vue'

  const { t } = useI18n()
  const store = useSolutionsStore()

  onMounted(() => {
    store.fetchSolutions()
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
    color: rgba(var(--v-theme-on-surface), 0.5);
  }
</style>
