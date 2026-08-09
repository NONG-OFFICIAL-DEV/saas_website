<template>
  <Transition name="dock-fade">
    <div v-if="visible" class="cta-dock" role="complementary" :aria-label="t('dock.trial')">
      <a
        href="https://t.me/Nong_Phloeut"
        target="_blank"
        rel="noopener"
        class="dock-btn dock-btn--telegram"
        :aria-label="t('dock.telegram')"
      >
        <v-icon icon="mdi-send-outline" size="18" />
        <span class="dock-label">{{ t('dock.telegram') }}</span>
      </a>
      <router-link to="/auth/register?intent=trial" class="dock-btn dock-btn--demo" :aria-label="t('dock.trial')">
        <v-icon icon="mdi-rocket-launch-outline" size="18" />
        <span class="dock-label">{{ t('dock.trial') }}</span>
      </router-link>
    </div>
  </Transition>
</template>

<script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  import { useI18n } from 'vue-i18n'

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
  box-shadow: 0 10px 28px rgba(var(--v-theme-on-surface), 0.18);
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
  background: rgb(var(--v-theme-primary));
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
    background: rgba(var(--v-theme-surface), 0.96);
    backdrop-filter: blur(14px);
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.08);
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
  .dock-btn :deep(.v-icon) {
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
