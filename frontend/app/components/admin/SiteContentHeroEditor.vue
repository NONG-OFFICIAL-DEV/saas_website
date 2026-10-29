<template>
  <div>
    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="120px" />

    <section v-else class="editor-section">
      <div class="field-grid">
        <div class="field">
          <Label>Badge text</Label>
          <Input v-model="htr.badge_text" />
        </div>
        <div class="field">
          <Label>Headline</Label>
          <Input v-model="htr.headline" />
        </div>
        <div class="field">
          <Label>Subheadline</Label>
          <Input v-model="htr.subheadline" />
        </div>
        <div class="field field--full">
          <Label>Description</Label>
          <Textarea v-model="htr.description" rows="2" />
        </div>
        <div class="field field--full">
          <Label>Trust line</Label>
          <Input v-model="htr.trust_line" />
        </div>
        <div class="field field--third">
          <Label>Primary CTA label</Label>
          <Input v-model="htr.cta_primary_label" />
        </div>
        <div class="field field--third">
          <Label>Secondary CTA label</Label>
          <Input v-model="htr.cta_secondary_label" />
        </div>
        <div class="field field--third">
          <Label>Secondary CTA URL</Label>
          <Input v-model="heroData.cta_secondary_url" />
        </div>
      </div>

      <h3 class="sub-heading">Stats</h3>
      <div v-for="(s, i) in htr.stats" :key="i" class="repeat-row">
        <Input v-model="s.num" placeholder="Value" />
        <Input v-model="s.label" placeholder="Label" />
        <Button size="icon-sm" variant="ghost" @click="htr.stats.splice(i, 1)">
          <Icon name="mdi-close" size="16" />
        </Button>
      </div>
      <Button size="sm" variant="secondary" @click="htr.stats.push({ num: '', label: '' })">
        <Icon name="mdi-plus" size="16" />
        Add stat
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
import { Textarea } from '~/components/ui/textarea'
import { getHeroForEdit, updateHeroForLocale, DATA_KEYS } from '~/services/cms/siteContent'
import { FALLBACK_HERO } from '~/stores/siteContent'

const props = defineProps<{
  activeLocale: string
  locales: { code: string, label: string }[]
}>()

const notify = useNotif()
const loading = ref(true)
const saving = ref(false)
const error = ref<string | null>(null)

// Splits the flat fallback object (data + content merged, matching the old
// single-locale shape) back into its two halves using the same DATA_KEYS
// the backend/service already use — so the initial "en" translation shows
// the site's real starting copy instead of blank fields.
function splitFallback() {
  const dataKeys = DATA_KEYS.hero ?? []
  const data: Record<string, any> = {}
  const content: Record<string, any> = {}
  for (const [k, v] of Object.entries(FALLBACK_HERO)) {
    if (k === 'id') continue
    ;(dataKeys.includes(k) ? data : content)[k] = v
  }
  return { data, content }
}
const FALLBACK = splitFallback()

function blankTr() {
  return {
    badge_text: '',
    headline: '',
    subheadline: '',
    description: '',
    trust_line: '',
    cta_primary_label: '',
    cta_secondary_label: '',
    stats: [] as { num: string, label: string }[]
  }
}

const heroData = reactive<Record<string, any>>({ cta_secondary_url: '', ...FALLBACK.data })
const translationsByLocale = reactive<Record<string, ReturnType<typeof blankTr>>>(
  Object.fromEntries(props.locales.map((l) => [l.code, l.code === 'en' ? { ...blankTr(), ...FALLBACK.content } : blankTr()]))
)
const htr = computed(() => translationsByLocale[props.activeLocale]!)

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    const hero = await getHeroForEdit()
    if (hero) {
      Object.assign(heroData, hero.data)
      for (const loc of props.locales) {
        const found = hero.translations.find((t) => t.locale === loc.code)
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
    await updateHeroForLocale(heroData, htr.value, props.activeLocale)
    notify('Hero section saved', { type: 'success' })
  } catch (err: any) {
    notify(err.message || 'Failed to save hero section', { type: 'error' })
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
  grid-template-columns: repeat(2, 1fr);
  gap: 12px;
}
.field--full {
  grid-column: 1 / -1;
}
.field--third {
  grid-column: span 1;
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
