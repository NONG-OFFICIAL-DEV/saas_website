<template>
  <div class="field">
    <Label v-if="label">{{ label }}</Label>
    <div class="file-input-row">
      <input
        type="file"
        :accept="accept"
        :disabled="loading"
        class="file-input"
        @change="$emit('change', $event)"
      />
      <span v-if="loading" class="file-input-spinner" role="status" aria-label="Uploading" />
    </div>
  </div>
</template>

<script setup lang="ts">
// Vuetify's <v-file-input> replacement — shadcn-vue ships no file-input
// primitive, so this wraps a plain <input type="file"> styled to match
// the rest of the form controls, keeping the same label/accept/loading/
// @change API so call sites converted 1:1.
import { Label } from '~/components/ui/label'

defineProps<{
  label?: string
  accept?: string
  loading?: boolean
}>()
defineEmits<{ change: [Event] }>()
</script>

<style scoped>
.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}
.file-input-row {
  display: flex;
  align-items: center;
  gap: 10px;
}
.file-input {
  font-size: 0.85rem;
  color: var(--foreground);
  max-width: 100%;
}
.file-input::file-selector-button {
  margin-right: 10px;
  padding: 6px 14px;
  border-radius: 8px;
  border: 1px solid var(--border);
  background: var(--secondary);
  color: var(--secondary-foreground);
  font-size: 0.8rem;
  font-weight: 600;
  cursor: pointer;
  transition: background 0.15s;
}
.file-input::file-selector-button:hover {
  background: color-mix(in srgb, var(--secondary) 80%, black 8%);
}
.file-input-spinner {
  display: inline-block;
  width: 16px;
  height: 16px;
  flex-shrink: 0;
  border-radius: 50%;
  border: 2px solid var(--primary);
  border-top-color: transparent;
  animation: file-input-spin 0.7s linear infinite;
}
@keyframes file-input-spin {
  to {
    transform: rotate(360deg);
  }
}
</style>
