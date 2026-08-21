<template>
  <div class="grid grid-cols-12" :class="[dense ? 'gap-3' : 'gap-6', alignClass, reverseClass]">
    <slot />
  </div>
</template>

<script setup lang="ts">
// Vuetify's <v-row> replacement — same 12-column grid + `dense` prop
// (Vuetify's default gutter is 24px, dense halves it to 12px) + `align`
// prop (maps onto the grid's own align-items, same visual effect as
// Vuetify's flexbox align-items) + `reverse` (replaces Vuetify's
// `direction="row-reverse"` for the two-column case: only swaps the
// first/last child's visual order once columns sit side-by-side at
// `md`+ — on mobile, where columns stack full-width, DOM order already
// matches what row-reverse rendered, since a wrapped flex row-reverse
// with one item per line doesn't change the stacking order).
const props = defineProps<{
  dense?: boolean
  align?: 'start' | 'center' | 'end'
  reverse?: boolean
}>()

const alignClass = computed(() => {
  if (props.align === 'center') return 'items-center'
  if (props.align === 'start') return 'items-start'
  if (props.align === 'end') return 'items-end'
  return ''
})

const reverseClass = computed(() =>
  props.reverse ? 'md:[&>*:first-child]:order-2 md:[&>*:last-child]:order-1' : ''
)
</script>
