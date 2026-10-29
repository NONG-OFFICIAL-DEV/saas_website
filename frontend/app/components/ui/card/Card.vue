<script setup lang="ts">
import type { HTMLAttributes } from 'vue'
import { cn } from '@/lib/utils'

const props = withDefaults(defineProps<{
  class?: HTMLAttributes['class']
  size?: 'default' | 'sm'
  /** Adds a hover lift + deeper shadow for cards that act as a link/button. */
  interactive?: boolean
}>(), {
  size: 'default',
  interactive: false,
})
</script>

<template>
  <div
    data-slot="card"
    :data-size="size"
    :data-interactive="interactive"
    :class="cn(
      'ring-foreground/10 bg-card text-card-foreground gap-4 overflow-hidden rounded-xl py-4 text-sm shadow-sm ring-1 transition-[transform,box-shadow] duration-base ease-out-quart has-data-[slot=card-footer]:pb-0 has-[>img:first-child]:pt-0 data-[size=sm]:gap-3 data-[size=sm]:py-3 data-[size=sm]:has-data-[slot=card-footer]:pb-0 *:[img:first-child]:rounded-t-xl *:[img:last-child]:rounded-b-xl group/card flex flex-col',
      interactive && 'cursor-pointer hover:-translate-y-1 hover:shadow-lg',
      props.class,
    )"
  >
    <slot />
  </div>
</template>
