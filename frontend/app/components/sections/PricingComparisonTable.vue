<template>
  <div class="compare-block">
    <RevealOnScroll as="div" class="text-center compare-header">
      <h2 class="section-title">{{ title }}</h2>
      <p class="section-sub compare-sub">{{ subtitle }}</p>
    </RevealOnScroll>

    <RevealOnScroll as="div" class="compare-scroll" :delay="80">
      <table class="compare-table">
        <thead>
          <tr>
            <th class="feature-col">{{ featureLabel }}</th>
            <th v-for="p in plans" :key="p.code" scope="col">{{ p.name }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="row in rows" :key="row.key">
            <td class="feature-col">{{ row.label }}</td>
            <td v-for="p in plans" :key="p.code">
              <Icon v-if="row.values[p.code] === true" name="mdi-check" size="16" class="cell-check" />
              <span v-else-if="typeof row.values[p.code] === 'string'" class="cell-value">{{ row.values[p.code] }}</span>
              <span v-else class="cell-dash">—</span>
            </td>
          </tr>
        </tbody>
      </table>
    </RevealOnScroll>
  </div>
</template>

<script setup lang="ts">
  withDefaults(
    defineProps<{
      title: string
      subtitle: string
      featureLabel?: string
      plans: { code: string; name: string }[]
      rows: { key: string; label: string; values: Record<string, boolean | string> }[]
    }>(),
    { featureLabel: 'Feature' }
  )
</script>

<style scoped>
  .compare-header {
    max-width:900px;
    margin: 0 auto 32px;
  }
  .compare-sub {
    max-width: 460px;
    margin: 0 auto;
  }

  .compare-scroll {
    overflow-x: auto;
    max-width: 900px;
    margin: 0 auto;
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    border-radius: var(--radius-xl);
  }

  .compare-table {
    width: 100%;
    min-width: 480px;
    border-collapse: collapse;
    font-size: 0.86rem;
  }
  .compare-table th,
  .compare-table td {
    padding: 12px 16px;
    text-align: center;
    white-space: nowrap;
  }
  .compare-table thead th {
    font-weight: 800;
    font-size: 0.82rem;
    background: color-mix(in srgb, var(--foreground) 4%, transparent);
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
  }
  .compare-table .feature-col {
    text-align: left;
    font-weight: 600;
    white-space: normal;
  }
  .compare-table tbody tr:not(:last-child) td {
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
  }
  .cell-check {
    color: var(--primary);
  }
  .cell-value {
    font-weight: 700;
  }
  .cell-dash {
    color: color-mix(in srgb, var(--foreground) 30%, transparent);
  }
</style>
