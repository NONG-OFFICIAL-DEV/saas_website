<template>
  <section class="section-pad section-tint-lavender" id="backoffice">
    <Container>
      <Row align="center">
        <!-- ── Text column ── -->
        <Col cols="12" md="5" data-aos="fade-right">
          <div class="section-tag">{{ t('home.inventory.tag') }}</div>
          <h2 class="section-title">{{ t('home.inventory.title') }}</h2>
          <p class="section-sub mb-7">{{ t('home.inventory.sub') }}</p>
          <FeatureChecks :items="checks" />
        </Col>

        <!-- ── Visual column ── -->
        <Col cols="12" md="7" data-aos="fade-left" data-aos-delay="100">
          <VisualWrapper color="teal">
            <div class="chart-mock">
              <!-- Header -->
              <div class="chart-header">
                <span class="chart-title">
                  <Icon name="mdi-chart-bar" size="16" class="mr-1" color="#14b8a6" />
                  Stock Movements
                </span>
                <span class="chart-badge">
                  <Icon name="mdi-calendar-month-outline" size="11" class="mr-1" />
                  This Month
                </span>
              </div>

              <!-- Bar chart -->
              <div class="chart-bars">
                <div v-for="(b, i) in chartBars" :key="i" class="bar-wrap">
                  <div class="bar" :style="`height:${b.h}%`" :class="{ 'bar-highlight': b.h === 90 }" />
                  <div class="bar-label">{{ b.l }}</div>
                </div>
              </div>

              <!-- Stock list -->
              <div class="stock-list">
                <div v-for="s in stockList" :key="s.name" class="stock-row">
                  <div class="flex items-center gap-2">
                    <Icon :name="s.icon" :color="s.color" size="15" />
                    <span class="stock-name">{{ s.name }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <div class="stock-bar-bg">
                      <div class="stock-bar-fill" :style="`width:${s.pct}%;background:${s.barColor}`" />
                    </div>
                    <span class="stock-qty" :style="`color:${s.color}`">{{ s.qty }}</span>
                  </div>
                </div>
              </div>
            </div>
          </VisualWrapper>
        </Col>
      </Row>
    </Container>
  </section>
</template>

<script setup lang="ts">
const { t } = useI18n()

const checks = computed(() =>
  ['f1', 'f2', 'f3', 'f4'].map((k) => t(`home.inventory.${k}`))
)

const chartBars = [
  { h: 38, l: 'W1' },
  { h: 55, l: 'W2' },
  { h: 48, l: 'W3' },
  { h: 90, l: 'W4' },
  { h: 70, l: 'W5' },
  { h: 82, l: 'W6' },
  { h: 60, l: 'W7' },
]

const stockList = [
  { name: 'Cambodian Beer', icon: 'mdi-check-circle', color: '#22c55e', barColor: '#22c55e', qty: '48',  pct: 80 },
  { name: 'Red Bull',       icon: 'mdi-check-circle', color: '#22c55e', barColor: '#22c55e', qty: '120', pct: 95 },
  { name: 'Heineken',       icon: 'mdi-alert',        color: '#f59e0b', barColor: '#f59e0b', qty: '3',   pct: 12 },
  { name: 'Mineral Water',  icon: 'mdi-close-circle', color: '#ef4444', barColor: '#ef4444', qty: '0',   pct: 0  },
]
</script>

<style scoped>
.chart-mock {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 16px 48px rgba(0,0,0,0.1);
}
.chart-header {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 16px;
}
.chart-title { font-size: 0.82rem; font-weight: 800; color: #1e293b; display: flex; align-items: center; }
.chart-badge {
  font-size: 0.65rem; background: #f0fdf4; color: #16a34a;
  padding: 3px 8px; border-radius: 999px; font-weight: 700;
  display: flex; align-items: center;
}

.chart-bars {
  display: flex; align-items: flex-end; gap: 6px;
  height: 80px; margin-bottom: 16px;
}
.bar-wrap {
  flex: 1; display: flex; flex-direction: column;
  align-items: center; gap: 4px; height: 100%;
}
.bar {
  width: 100%;
  background: rgba(20,184,166,0.35);
  border-radius: 4px 4px 0 0;
  transition: height 0.3s;
}
.bar-highlight { background: #14b8a6 !important; }
.bar-label { font-size: 0.58rem; color: #94a3b8; font-weight: 600; }

.stock-list  { display: flex; flex-direction: column; gap: 8px; }
.stock-row   { display: flex; justify-content: space-between; align-items: center; }
.stock-name  { font-size: 0.72rem; font-weight: 600; color: #334155; }
.stock-bar-bg {
  width: 72px; height: 5px; background: #f1f5f9;
  border-radius: 99px; overflow: hidden;
}
.stock-bar-fill { height: 100%; border-radius: 99px; transition: width 0.5s; }
.stock-qty { font-size: 0.7rem; font-weight: 800; min-width: 24px; text-align: right; }
</style>
