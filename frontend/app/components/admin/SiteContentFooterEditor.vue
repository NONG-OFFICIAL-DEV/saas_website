<template>
  <div>
    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="120px" />

    <section v-else class="editor-section">
      <div class="field-grid">
        <div class="field">
          <Label>Email</Label>
          <Input v-model="footerData.email" />
        </div>
        <div class="field">
          <Label>Phone</Label>
          <Input v-model="footerData.phone" />
        </div>
        <div class="field">
          <Label>Address</Label>
          <Input v-model="ftr.address" />
        </div>
      </div>

      <h3 class="sub-heading">Social links</h3>
      <p class="hint">Names "Telegram" / "Facebook" / "TikTok" get their matching icon automatically; anything else gets a generic link icon.</p>
      <div v-for="(s, i) in footerData.socials" :key="i" class="repeat-row">
        <Input v-model="s.name" placeholder="Name" />
        <Input v-model="s.href" placeholder="URL" />
        <Button size="icon-sm" variant="ghost" @click="footerData.socials.splice(i, 1)">
          <Icon name="mdi-close" size="16" />
        </Button>
      </div>
      <Button size="sm" variant="secondary" @click="footerData.socials.push({ name: '', href: '' })">
        <Icon name="mdi-plus" size="16" />
        Add social link
      </Button>

      <div class="save-row">
        <Button :disabled="saving" @click="save">
          <Icon v-if="saving" name="mdi-loading" size="16" class="animate-spin" />
          Save
        </Button>
      </div>
    </section>
  </div>
</template>

<script setup lang="ts">
import { Alert, AlertDescription } from '~/components/ui/alert'
import { Button } from '~/components/ui/button'
import { Input } from '~/components/ui/input'
import { Label } from '~/components/ui/label'
import { getFooterForEdit, updateFooterForLocale, DATA_KEYS } from '~/services/cms/siteContent'
import { FALLBACK_FOOTER } from '~/stores/siteContent'

const props = defineProps<{
  activeLocale: string
  locales: { code: string, label: string }[]
}>()

const notify = useNotif()
const loading = ref(true)
const saving = ref(false)
const error = ref<string | null>(null)

function splitFallback() {
  const dataKeys = DATA_KEYS.footer ?? []
  const data: Record<string, any> = {}
  const content: Record<string, any> = {}
  for (const [k, v] of Object.entries(FALLBACK_FOOTER)) {
    if (k === 'id') continue
    ;(dataKeys.includes(k) ? data : content)[k] = v
  }
  return { data, content }
}
const FALLBACK = splitFallback()

function blankTr() {
  return { address: '' }
}

const footerData = reactive<Record<string, any>>({
  email: '',
  phone: '',
  socials: [] as { name: string, href: string }[],
  ...FALLBACK.data
})
const translationsByLocale = reactive<Record<string, ReturnType<typeof blankTr>>>(
  Object.fromEntries(props.locales.map((l) => [l.code, l.code === 'en' ? { ...blankTr(), ...FALLBACK.content } : blankTr()]))
)
const ftr = computed(() => translationsByLocale[props.activeLocale]!)

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    const footer = await getFooterForEdit()
    if (footer) {
      Object.assign(footerData, footer.data)
      for (const loc of props.locales) {
        const found = footer.translations.find((t) => t.locale === loc.code)
        const base = loc.code === 'en' ? { ...blankTr(), ...FALLBACK.content } : blankTr()
        translationsByLocale[loc.code] = found ? { ...base, ...found } : base
      }
    }
  } catch (err: any) {
    error.value = err.message
  } finally {
    loading.value = false
  }
})

async function save() {
  saving.value = true
  error.value = null
  try {
    await updateFooterForLocale(footerData, ftr.value, props.activeLocale)
    notify('Footer saved', { type: 'success' })
  } catch (err: any) {
    notify(err.message || 'Failed to save footer', { type: 'error' })
  } finally {
    saving.value = false
  }
}
</script>

<style scoped>
.editor-section {
  padding: 24px;
  border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
  border-radius: var(--radius-lg);
  background: color-mix(in srgb, var(--card) 60%, transparent);
}

.field-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 12px;
}
@media (max-width: 640px) {
  .field-grid {
    grid-template-columns: 1fr;
  }
}

.field {
  display: flex;
  flex-direction: column;
  gap: 6px;
}

.sub-heading {
  font-size: 0.9rem;
  font-weight: 800;
  margin: 24px 0 12px;
}
.hint {
  font-size: 0.8rem;
  color: color-mix(in srgb, var(--foreground) 55%, transparent);
  margin: 0 0 12px;
}

.repeat-row {
  display: grid;
  grid-template-columns: 1fr 1fr auto;
  gap: 8px;
  align-items: center;
  margin-bottom: 6px;
}
@media (max-width: 640px) {
  .repeat-row {
    grid-template-columns: 1fr;
  }
}

.save-row {
  margin-top: 24px;
}
</style>
