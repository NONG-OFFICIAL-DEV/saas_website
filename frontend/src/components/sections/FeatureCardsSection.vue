<template>
  <section class="section-pad bg-soft">
    <v-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <div class="section-tag">{{ t('landing.features.tag') }}</div>
        <h2 class="section-title">{{ t('landing.features.title') }}</h2>
      </div>

      <v-row dense>
        <v-col
          v-for="(f, i) in featureCards"
          :key="f.titleKey"
          cols="12" sm="6" md="4"
          data-aos="fade-up"
          :data-aos-delay="i * 60"
        >
          <v-card rounded="xl" border variant="flat" class="feature-card pa-7 fill-height" hover>
            <v-avatar :color="f.color" variant="tonal" rounded="xl" size="52" class="mb-5">
              <v-icon :icon="f.icon" size="24" />
            </v-avatar>
            <div class="text-body-1 font-weight-bold mb-2">{{ t(f.titleKey) }}</div>
            <div class="text-body-2 text-medium-emphasis" style="line-height:1.7">
              {{ t(f.descKey) }}
            </div>
          </v-card>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'

const { t } = useI18n()

/**
 * To add or remove cards: edit this array only.
 * No template changes needed.
 */
const featureCards = [
  { icon: 'mdi-qrcode-scan',       color: 'primary', titleKey: 'landing.features.qr_title',       descKey: 'landing.features.qr_desc'       },
  { icon: 'mdi-chef-hat',          color: 'teal',    titleKey: 'landing.features.kitchen_title',  descKey: 'landing.features.kitchen_desc'  },
  { icon: 'mdi-source-branch',    color: 'indigo',  titleKey: 'landing.features.branch_title',   descKey: 'landing.features.branch_desc'   },
  { icon: 'mdi-printer-outline',   color: 'purple',  titleKey: 'landing.features.printing_title', descKey: 'landing.features.printing_desc' },
  { icon: 'mdi-chart-line',        color: 'amber',   titleKey: 'landing.features.analytics_title',descKey: 'landing.features.analytics_desc'},
  { icon: 'mdi-credit-card-outline', color: 'cyan',  titleKey: 'landing.features.payments_title', descKey: 'landing.features.payments_desc' },
]
</script>

<style scoped>
.feature-card {
  transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}
.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 32px rgba(0,0,0,0.07) !important;
}
</style>