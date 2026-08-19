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
        <router-link v-for="item in items" :key="item.to" :to="item.to" class="dropdown-item">
          <v-icon v-if="item.icon" :icon="item.icon" size="18" class="dropdown-item-icon" />
          <span class="dropdown-item-text">
            <span class="dropdown-item-label">{{ item.label }}</span>
            <span v-if="item.description" class="dropdown-item-desc">{{ item.description }}</span>
          </span>
        </router-link>

        <router-link v-if="viewAllTo" :to="viewAllTo" class="dropdown-view-all">
          {{ viewAllLabel }}
          <v-icon icon="mdi-arrow-right" size="14" />
        </router-link>
      </div>
    </Transition>
  </div>
</template>

<script setup>
  import { ref } from 'vue'

  defineProps({
    label: { type: String, required: true },
    items: { type: Array, default: () => [] },
    viewAllLabel: { type: String, default: '' },
    viewAllTo: { type: String, default: '' }
  })

  const isOpen = ref(false)
  let closeTimer = null

  function open() {
    clearTimeout(closeTimer)
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
    color: rgba(var(--v-theme-on-surface), 0.6);
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.15s;
  }
  .nav-dropdown-trigger:hover,
  .nav-dropdown-trigger.active {
    color: rgb(var(--v-theme-primary));
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
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    border-radius: 18px;
    box-shadow: 0 20px 44px rgba(var(--v-theme-on-surface), 0.14);
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
    color: rgb(var(--v-theme-on-surface));
    transition: background 0.15s;
  }
  .dropdown-item:hover {
    background: rgba(var(--v-theme-primary), 0.06);
  }
  .dropdown-item-icon {
    margin-top: 2px;
    color: rgb(var(--v-theme-primary));
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
    color: rgba(var(--v-theme-on-surface), 0.55);
    line-height: 1.4;
  }

  .dropdown-view-all {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    margin-top: 6px;
    padding: 10px 12px;
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    font-size: 0.82rem;
    font-weight: 700;
    color: rgb(var(--v-theme-primary));
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
