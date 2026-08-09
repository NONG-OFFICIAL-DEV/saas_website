<template>
  <section class="section-pad bg-soft" id="features">
    <v-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <div class="section-tag">{{ t('landing.biz.tag') }}</div>
        <h2 class="section-title">{{ t('landing.biz.title') }}</h2>
      </div>

      <v-row justify="center" class="px-md-10">
        <v-col
          v-for="(biz, i) in bizTypes"
          :key="biz.key"
          cols="6" sm="4" md="2"
          data-aos="fade-up"
          :data-aos-delay="i * 100"
        >
          <div class="biz-card-wrapper">
            <v-card variant="flat" class="biz-card-modern" hover>
              <!-- Icon Container with Dynamic Gradient -->
              <div class="icon-box" :style="{ background: biz.grad }">
                <v-icon :icon="biz.icon" color="white" size="32" />
              </div>
              
              <div class="biz-label-modern">{{ t(biz.key) }}</div>
              <div class="active-line"></div>
            </v-card>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </section>
</template>

<script setup>
import { useI18n } from 'vue-i18n'
const { t } = useI18n()

const bizTypes = [
  { 
    icon: 'mdi-silverware-clean', 
    grad: 'linear-gradient(135deg, #FF5F6D 0%, #FFC371 100%)', 
    key: 'landing.biz.restaurant' 
  },
  { 
    icon: 'mdi-coffee-to-go-outline', 
    grad: 'linear-gradient(135deg, #614385 0%, #516395 100%)', 
    key: 'landing.biz.cafe' 
  },
  { 
    icon: 'mdi-basket-outline', 
    grad: 'linear-gradient(135deg, #11998e 0%, #38ef7d 100%)', 
    key: 'landing.biz.minimart' 
  },
  { 
    icon: 'mdi-tshirt-crew-outline', 
    grad: 'linear-gradient(135deg, #4568DC 0%, #B06AB3 100%)', 
    key: 'landing.biz.retail' 
  },
  { 
    icon: 'mdi-muffin', 
    grad: 'linear-gradient(135deg, #f83600 0%, #f9d423 100%)', 
    key: 'landing.biz.bakery' 
  },
]
</script>

<style scoped>
.biz-card-wrapper {
  perspective: 1000px;
}

.biz-card-modern {
  background: white !important;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08) !important;
  border-radius: 28px !important;
  padding: 30px 10px;
  text-align: center;
  transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
  position: relative;
  overflow: hidden !important;
}

.icon-box {
  width: 64px;
  height: 64px;
  margin: 0 auto 16px;
  border-radius: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 10px 20px -5px rgba(0,0,0,0.15);
  transition: transform 0.5s ease;
}

.biz-label-modern {
  font-size: 0.9rem;
  font-weight: 800;
  color: #2c3e50;
  letter-spacing: -0.3px;
}

/* Hover Effects */
.biz-card-modern:hover {
  transform: translateY(-10px) rotateX(5deg);
  border-color: rgba(var(--v-theme-primary), 0.3) !important;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1) !important;
}

.biz-card-modern:hover .icon-box {
  transform: scale(1.1) rotate(-8deg);
}

/* Decoration line that appears on hover */
.active-line {
  position: absolute;
  bottom: 0;
  left: 50%;
  width: 0;
  height: 4px;
  background: rgb(var(--v-theme-primary));
  transition: all 0.3s ease;
  transform: translateX(-50%);
  border-radius: 4px 4px 0 0;
}

.biz-card-modern:hover .active-line {
  width: 40px;
}
</style>