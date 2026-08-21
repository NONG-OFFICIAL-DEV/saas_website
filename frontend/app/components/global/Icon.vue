<template>
  <i class="mdi" :class="name" :style="style" aria-hidden="true" />
</template>

<script setup lang="ts">
// Thin wrapper preserving @mdi/font icons (Material Design Icons) instead
// of switching to lucide-vue-next — swapping icon sets would be a visual
// redesign of ~90 call sites (different glyphs/stroke weights, no 1:1
// equivalents for several), which the Tailwind/shadcn-vue migration is
// explicitly not supposed to be. Every old `<v-icon icon="mdi-x">` becomes
// `<Icon name="mdi-x">` — same icon, same font, just not a Vuetify component.
const props = withDefaults(
  defineProps<{
    name: string
    size?: number | string
    color?: string
  }>(),
  { size: 24, color: 'currentColor' }
)

const style = computed(() => {
  // The mechanical v-icon -> Icon conversion carries size="18" over
  // unquoted (a plain string, not a number binding) — treat a purely
  // numeric string the same as a number so it still gets a px unit,
  // while an explicit unit string (e.g. "1.2rem") passes through as-is.
  const size = props.size
  const isNumeric = typeof size === 'number' || /^\d+(\.\d+)?$/.test(String(size))
  return {
    fontSize: isNumeric ? `${size}px` : size,
    color: props.color,
    lineHeight: 1
  }
})
</script>
