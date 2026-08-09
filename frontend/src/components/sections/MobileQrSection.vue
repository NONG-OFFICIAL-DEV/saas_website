<template>
  <section class="section-pad section-tint-mint" id="mobile">
    <v-container>
      <v-row align="center" direction="row-reverse">
        <v-col cols="12" md="6" data-aos="fade-right" data-aos-delay="100">
          <img class="qr_image" src="/images/qr_scan.webp" width="400" alt="" />
        </v-col>
        <v-col cols="12" md="6" data-aos="fade-left">
          <div class="section-tag">{{ t('scan_menu.tag') }}</div>
          <h2 class="section-title">{{ t('scan_menu.title') }}</h2>
          <p class="section-sub mb-7">{{ t('scan_menu.subtitle') }}</p>
          <FeatureChecks :items="checks" />
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script setup>
  import { computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import FeatureChecks from '@/components/ui/FeatureChecks.vue'

  const { t } = useI18n()

  const checks = computed(() =>
    ['feat1', 'feat2', 'feat3', 'feat4'].map(k => t(`scan_menu.${k}`))
  )
</script>
<style scoped>

  @media (max-width: 500px) {
    .qr_image {
      width: 300px;
    }
  }
</style>
