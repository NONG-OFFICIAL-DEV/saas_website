import { defineStore } from 'pinia'
import { ref, computed } from 'vue'

// Reference-counted (not a boolean) so N concurrent 'bar'-opted-in calls
// don't hide the bar the instant the first one finishes — it only clears
// once every started call has stopped.
export const useLoadingStore = defineStore('loading', () => {
  const count = ref(0)
  const mode = ref('bar') // only mode today — a thin top progress bar, never a blocking overlay

  const isLoading = computed(() => count.value > 0)

  function start(type = 'bar') {
    mode.value = type
    count.value++
  }

  function stop() {
    count.value = Math.max(0, count.value - 1)
    return count.value
  }

  return {
    isLoading,
    mode,
    start,
    stop
  }
})
