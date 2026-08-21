<template>
  <div :class="classes">
    <slot />
  </div>
</template>

<script setup lang="ts">
// Vuetify's <v-col cols="12" sm="6"> replacement — same breakpoint-prop
// API (cols/sm/md/lg/xl), translated to Tailwind's grid col-span utilities.
//
// Tailwind's build-time scanner only generates CSS for class names that
// appear as a literal substring somewhere in a source file — a runtime
// template-string like `col-span-${cols}` is invisible to it. The full set
// of possible literal classes is listed below (never rendered) purely so
// the scanner sees them and generates the CSS; the actual class used at
// runtime is still built dynamically from props, and will match one of
// these already-generated classes exactly.
//
// col-span-1 col-span-2 col-span-3 col-span-4 col-span-5 col-span-6
// col-span-7 col-span-8 col-span-9 col-span-10 col-span-11 col-span-12
// sm:col-span-1 sm:col-span-2 sm:col-span-3 sm:col-span-4 sm:col-span-5
// sm:col-span-6 sm:col-span-7 sm:col-span-8 sm:col-span-9 sm:col-span-10
// sm:col-span-11 sm:col-span-12
// md:col-span-1 md:col-span-2 md:col-span-3 md:col-span-4 md:col-span-5
// md:col-span-6 md:col-span-7 md:col-span-8 md:col-span-9 md:col-span-10
// md:col-span-11 md:col-span-12
// lg:col-span-1 lg:col-span-2 lg:col-span-3 lg:col-span-4 lg:col-span-5
// lg:col-span-6 lg:col-span-7 lg:col-span-8 lg:col-span-9 lg:col-span-10
// lg:col-span-11 lg:col-span-12
// xl:col-span-1 xl:col-span-2 xl:col-span-3 xl:col-span-4 xl:col-span-5
// xl:col-span-6 xl:col-span-7 xl:col-span-8 xl:col-span-9 xl:col-span-10
// xl:col-span-11 xl:col-span-12

const props = defineProps<{
  cols?: string | number
  sm?: string | number
  md?: string | number
  lg?: string | number
  xl?: string | number
}>()

const classes = computed(() => {
  const parts: string[] = ['col-span-12']
  if (props.cols) parts[0] = `col-span-${props.cols}`
  if (props.sm) parts.push(`sm:col-span-${props.sm}`)
  if (props.md) parts.push(`md:col-span-${props.md}`)
  if (props.lg) parts.push(`lg:col-span-${props.lg}`)
  if (props.xl) parts.push(`xl:col-span-${props.xl}`)
  return parts.join(' ')
})
</script>
