<template>
  <section class="section-pad coming-soon">
    <v-container class="text-center">
      <v-icon icon="mdi-hammer-wrench" size="44" />
      <h1 class="section-title">{{ title }}</h1>
      <p class="section-sub mx-auto">{{ t('coming_soon.desc') }}</p>
      <v-btn color="primary" rounded="lg" to="/">
        {{ t('button.back_to_home') }}
      </v-btn>
    </v-container>
  </section>
</template>

<script setup>
  import { computed } from 'vue'
  import { useI18n } from 'vue-i18n'

  const props = defineProps({
    titleKey: { type: String, default: '' }
  })

  const { t } = useI18n()
  const title = computed(() => (props.titleKey ? t(props.titleKey) : t('coming_soon.title')))
</script>

<style scoped>
  .coming-soon {
    padding-top: 140px;
    padding-bottom: 100px;
  }
  .section-sub {
    max-width: 420px;
    margin: 12px auto 28px;
  }
</style>
