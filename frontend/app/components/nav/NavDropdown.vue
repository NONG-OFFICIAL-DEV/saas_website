<template>
  <div class="nav-dropdown" @mouseenter="open" @mouseleave="scheduleClose">
    <button
      class="nav-dropdown-trigger"
      :class="{ active: isOpen }"
      :aria-expanded="isOpen"
      @click="toggle"
    >
      {{ label }}
      <svg
        class="chevron"
        :class="{ open: isOpen }"
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 24 24"
        width="12"
        height="12"
        fill="none"
        stroke="currentColor"
        stroke-width="2.5"
        stroke-linecap="round"
        stroke-linejoin="round"
      >
        <polyline points="6 9 12 15 18 9" />
      </svg>
    </button>

    <Transition name="dropdown-fade">
      <div v-if="isOpen" class="nav-dropdown-panel" @click="close">
        <NuxtLink v-for="item in items" :key="item.to" :to="item.to" class="dropdown-item">
          <Icon v-if="item.icon" :name="item.icon" size="18" class="dropdown-item-icon" />
          <span class="dropdown-item-text">
            <span class="dropdown-item-label">{{ item.label }}</span>
            <span v-if="item.description" class="dropdown-item-desc">{{ item.description }}</span>
          </span>
        </NuxtLink>

        <NuxtLink v-if="viewAllTo" :to="viewAllTo" class="dropdown-view-all">
          {{ viewAllLabel }}
          <Icon name="mdi-arrow-right" size="14" />
        </NuxtLink>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
  import type { NavItem } from '~/types'

  withDefaults(
    defineProps<{
      label: string
      items?: NavItem[]
      viewAllLabel?: string
      viewAllTo?: string
    }>(),
    {
      items: () => [],
      viewAllLabel: '',
      viewAllTo: ''
    }
  )

  const isOpen = ref(false)
  let closeTimer: ReturnType<typeof setTimeout> | null = null

  function open() {
    if (closeTimer) clearTimeout(closeTimer)
    isOpen.value = true
  }
  function scheduleClose() {
    closeTimer = setTimeout(() => {
      isOpen.value = false
    }, 150)
  }
  function toggle() {
    isOpen.value = !isOpen.value
  }
  function close() {
    isOpen.value = false
  }
</script>

<style scoped>
  .nav-dropdown {
    position: relative;
    height: 100%;
    display: flex;
    align-items: center;
  }

  .nav-dropdown-trigger {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 0.875rem;
    font-weight: 600;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.15s;
  }
  .nav-dropdown-trigger:hover,
  .nav-dropdown-trigger.active {
    color: var(--primary);
  }

  .chevron {
    opacity: 0.6;
    transition: transform 0.2s;
  }
  .chevron.open {
    transform: rotate(180deg);
  }

  .nav-dropdown-panel {
    position: absolute;
    top: calc(100% + 14px);
    left: 50%;
    transform: translateX(-50%);
    min-width: 280px;
    background: var(--card);
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    border-radius: 18px;
    box-shadow: 0 20px 44px color-mix(in srgb, var(--foreground) 14%, transparent);
    padding: 8px;
    z-index: 2000;
  }

  .dropdown-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 12px;
    text-decoration: none;
    color: var(--foreground);
    transition: background 0.15s;
  }
  .dropdown-item:hover {
    background: color-mix(in srgb, var(--primary) 6%, transparent);
  }
  .dropdown-item-icon {
    margin-top: 2px;
    color: var(--primary);
    flex-shrink: 0;
  }
  .dropdown-item-text {
    display: flex;
    flex-direction: column;
    gap: 2px;
    min-width: 0;
  }
  .dropdown-item-label {
    font-size: 0.86rem;
    font-weight: 700;
  }
  .dropdown-item-desc {
    font-size: 0.76rem;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    line-height: 1.4;
  }

  .dropdown-view-all {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 6px;
    padding: 10px 12px;
    border-top: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
    font-size: 0.82rem;
    font-weight: 700;
    color: var(--primary);
    text-decoration: none;
  }

  .dropdown-fade-enter-active,
  .dropdown-fade-leave-active {
    transition: all 0.18s ease;
  }
  .dropdown-fade-enter-from,
  .dropdown-fade-leave-to {
    opacity: 0;
    transform: translateX(-50%) translateY(8px);
  }
</style>
