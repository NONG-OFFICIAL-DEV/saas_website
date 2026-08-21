<template>
  <Transition name="dock-fade">
    <div v-if="visible" class="cta-dock" role="complementary" :aria-label="t('button.start_free_trial')">
      <a
        href="https://t.me/Nong_Phloeut"
        target="_blank"
        rel="noopener"
        class="dock-btn dock-btn--telegram"
        :aria-label="t('button.chat_telegram')"
      >
        <Icon name="mdi-send-outline" size="18" />
        <span class="dock-label">{{ t('button.chat_telegram') }}</span>
      </a>
      <NuxtLink
        to="/get-started"
        class="dock-btn dock-btn--demo"
        :aria-label="t('button.start_free_trial')"
      >
        <Icon name="mdi-rocket-launch-outline" size="18" />
        <span class="dock-label">{{ t('button.start_free_trial') }}</span>
      </NuxtLink>
    </div>
  </Transition>
</template>

<script setup lang="ts">
  const { t } = useI18n()
  const visible = ref(false)

  function onScroll() {
    visible.value = window.scrollY > 480
  }

  onMounted(() => window.addEventListener('scroll', onScroll, { passive: true }))
  onUnmounted(() => window.removeEventListener('scroll', onScroll))
</script>

<style scoped>
.cta-dock {
  position: fixed;
  z-index: 900;
  display: flex;
  gap: 10px;
  bottom: 65px;
  right: 24px;
  flex-direction: column;
  align-items: flex-end;
}

.dock-btn {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 12px 18px;
  border-radius: 999px;
  font-size: 0.82rem;
  font-weight: 700;
  text-decoration: none;
  box-shadow: 0 10px 28px color-mix(in srgb, var(--foreground) 18%, transparent);
  transition: transform 0.15s ease;
  white-space: nowrap;
}
.dock-btn:hover {
  transform: translateY(-2px);
}

.dock-btn--telegram {
  background: #229ed9;
  color: #fff;
}
.dock-btn--demo {
  background: var(--primary);
  color: #fff;
}

/* ── Mobile: collapse to a full-width sticky bottom bar ── */
@media (max-width: 640px) {
  .cta-dock {
    left: 0;
    right: 0;
    bottom: 0;
    flex-direction: row;
    gap: 6px;
    padding: 10px 10px calc(10px + env(safe-area-inset-bottom));
    background: color-mix(in srgb, var(--card) 96%, transparent);
    backdrop-filter: blur(14px);
    border-top: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
  }
  .dock-btn {
    flex: 1;
    justify-content: center;
    border-radius: 12px;
    padding: 10px 6px;
    gap: 5px;
    font-size: 0.72rem;
    min-width: 0;
  }
  .dock-label {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    min-width: 0;
  }
  .dock-btn :deep(.mdi) {
    font-size: 16px;
    flex-shrink: 0;
  }
}

@media (max-width: 360px) {
  .dock-btn {
    font-size: 0.66rem;
    padding: 10px 4px;
  }
}

/* ── Transition ── */
.dock-fade-enter-active,
.dock-fade-leave-active {
  transition: all 0.25s ease;
}
.dock-fade-enter-from,
.dock-fade-leave-to {
  opacity: 0;
  transform: translateY(16px);
}
</style>
