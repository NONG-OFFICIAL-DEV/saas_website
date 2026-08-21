<template>
  <section class="section-pad section-tint-sky" id="pos">
    <Container>
      <Row align="center">
        <!-- ── Text column ── -->
        <Col cols="12" md="5" data-aos="fade-right">
          <div class="section-tag">{{ t('pos.tag') }}</div>
          <h2 class="section-title">{{ t('pos.title') }}</h2>
          <p class="section-sub mb-7">{{ t('pos.sub') }}</p>
          <FeatureChecks :items="checks" />
        </Col>

        <!-- ── Visual column ── -->
        <Col cols="12" md="7" data-aos="fade-left" data-aos-delay="100">
          <VisualWrapper color="indigo">
            <div class="pos-mock">
              <div class="pos-header">
                <span class="pos-title">
                  <Icon name="mdi-table-furniture" size="16" class="mr-1" color="#6366f1" />
                  Table Overview
                </span>
                <span class="pos-badge">
                  <Icon name="mdi-circle" size="8" class="mr-1" color="#22c55e" />
                  Live
                </span>
              </div>

              <div class="table-grid">
                <div
                  v-for="tbl in tables"
                  :key="tbl.num"
                  class="table-chip"
                  :class="`table-${tbl.status}`"
                >
                  T{{ tbl.num }}
                </div>
              </div>

              <div class="order-ticket">
                <div class="order-ticket-title">Table 4 · Order #128</div>
                <div v-for="item in orderItems" :key="item.name" class="order-row">
                  <span class="order-item">{{ item.qty }}× {{ item.name }}</span>
                  <span class="order-price">${{ item.price }}</span>
                </div>
                <div class="order-total">
                  <span>Total</span>
                  <span>${{ orderTotal }}</span>
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

// Translate check items at render time
const checks = computed(() =>
  ['f1', 'f2', 'f3', 'f4'].map((k) => t(`pos.${k}`))
)

const tables = [
  { num: 1, status: 'occupied' },
  { num: 2, status: 'available' },
  { num: 3, status: 'available' },
  { num: 4, status: 'occupied' },
  { num: 5, status: 'reserved' },
  { num: 6, status: 'available' },
  { num: 7, status: 'occupied' },
  { num: 8, status: 'available' },
]

const orderItems = [
  { name: 'Fried Rice', qty: 2, price: '7.00' },
  { name: 'Iced Coffee', qty: 1, price: '2.50' },
  { name: 'Spring Rolls', qty: 1, price: '3.50' },
]

const orderTotal = computed(() =>
  orderItems.reduce((sum, i) => sum + i.qty * parseFloat(i.price), 0).toFixed(2)
)
</script>

<style scoped>
.pos-mock {
  background: #fff;
  border-radius: 16px;
  padding: 20px;
  width: 100%;
  max-width: 400px;
  box-shadow: 0 16px 48px rgba(0, 0, 0, 0.1);
}
.pos-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}
.pos-title {
  font-size: 0.82rem;
  font-weight: 800;
  color: #1e293b;
  display: flex;
  align-items: center;
}
.pos-badge {
  font-size: 0.65rem;
  background: #f0fdf4;
  color: #16a34a;
  padding: 3px 8px;
  border-radius: 999px;
  font-weight: 700;
  display: flex;
  align-items: center;
}

.table-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 8px;
  margin-bottom: 18px;
}
.table-chip {
  text-align: center;
  padding: 8px 0;
  border-radius: 8px;
  font-size: 0.72rem;
  font-weight: 800;
}
.table-available {
  background: #f0fdf4;
  color: #16a34a;
}
.table-occupied {
  background: #fef2f2;
  color: #dc2626;
}
.table-reserved {
  background: #fffbeb;
  color: #d97706;
}

.order-ticket {
  border-top: 1px dashed #e2e8f0;
  padding-top: 12px;
}
.order-ticket-title {
  font-size: 0.7rem;
  font-weight: 700;
  color: #94a3b8;
  margin-bottom: 8px;
}
.order-row {
  display: flex;
  justify-content: space-between;
  font-size: 0.75rem;
  color: #334155;
  padding: 3px 0;
}
.order-total {
  display: flex;
  justify-content: space-between;
  font-size: 0.82rem;
  font-weight: 800;
  color: #1e293b;
  margin-top: 8px;
  padding-top: 8px;
  border-top: 1px solid #e2e8f0;
}
</style>
