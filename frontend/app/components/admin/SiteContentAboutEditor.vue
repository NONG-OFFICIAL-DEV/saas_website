<template>
  <div>
    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="120px" />

    <section v-else class="editor-section">
      <h3 class="sub-heading sub-heading--first">Hero</h3>
      <div class="field-grid">
        <div class="field">
          <Label>Eyebrow tag</Label>
          <Input v-model="atr.hero_tag" />
        </div>
        <div class="field field--full">
          <Label>Heading</Label>
          <Input v-model="atr.hero_heading" />
        </div>
        <div class="field field--full">
          <Label>Description</Label>
          <Textarea v-model="atr.hero_description" rows="2" />
        </div>
        <div class="field">
          <Label>Primary CTA label</Label>
          <Input v-model="atr.hero_cta_primary_label" />
        </div>
        <div class="field">
          <Label>Secondary CTA label</Label>
          <Input v-model="atr.hero_cta_secondary_label" />
        </div>
      </div>

      <h3 class="sub-heading">Story ("Why I build these products")</h3>
      <div class="field-grid">
        <div class="field field--full">
          <Label>Title</Label>
          <Input v-model="atr.story_title" />
        </div>
        <div class="field field--full">
          <Label>Content (separate paragraphs with a blank line)</Label>
          <Textarea v-model="atr.story_content" rows="4" />
        </div>
      </div>

      <h3 class="sub-heading">What I Build (section intro — products themselves come from the Products page)</h3>
      <div class="field-grid field-grid--thirds">
        <div class="field">
          <Label>Eyebrow tag</Label>
          <Input v-model="atr.products_tag" />
        </div>
        <div class="field">
          <Label>Title</Label>
          <Input v-model="atr.products_title" />
        </div>
        <div class="field">
          <Label>Description</Label>
          <Input v-model="atr.products_description" />
        </div>
      </div>

      <h3 class="sub-heading">Approach ("How I build" — 4 cards)</h3>
      <div class="field-grid">
        <div class="field">
          <Label>Eyebrow tag</Label>
          <Input v-model="atr.approach_tag" />
        </div>
        <div class="field">
          <Label>Title</Label>
          <Input v-model="atr.approach_title" />
        </div>
      </div>
      <div v-for="(c, i) in atr.approach_cards" :key="i" class="repeat-row repeat-row--wide">
        <Input v-model="c.icon" placeholder="Icon (mdi-...)" />
        <Input v-model="c.title" placeholder="Title" />
        <Input v-model="c.description" placeholder="Description" />
        <Button size="icon-sm" variant="ghost" @click="atr.approach_cards.splice(i, 1)">
          <Icon name="mdi-close" size="16" />
        </Button>
      </div>
      <Button
        size="sm"
        variant="secondary"
        @click="atr.approach_cards.push({ icon: 'mdi-check-circle-outline', title: '', description: '' })"
      >
        <Icon name="mdi-plus" size="16" />
        Add card
      </Button>

      <h3 class="sub-heading">Who I Build For</h3>
      <div class="field-grid">
        <div class="field">
          <Label>Eyebrow tag</Label>
          <Input v-model="atr.audience_tag" />
        </div>
        <div class="field">
          <Label>Title</Label>
          <Input v-model="atr.audience_title" />
        </div>
        <div class="field field--full">
          <Label>Description</Label>
          <Input v-model="atr.audience_description" />
        </div>
      </div>
      <div v-for="(ex, i) in atr.audience_examples" :key="i" class="nested-card nested-card--shot">
        <img v-if="ex.image_url" :src="ex.image_url" class="shot-preview" :alt="ex.label" />
        <div class="field-grid grow">
          <div class="field field--quarter">
            <Label>Label</Label>
            <Input v-model="ex.label" />
          </div>
          <div class="field field--quarter">
            <Label>Fallback icon (mdi-...)</Label>
            <Input v-model="ex.icon" />
          </div>
          <div class="field field--half">
            <Label>Description</Label>
            <Input v-model="ex.description" />
          </div>
          <div class="field">
            <Label>Image URL</Label>
            <Input v-model="ex.image_url" placeholder="https://..." />
          </div>
          <div class="field">
            <Label>Or upload an image</Label>
            <FileInput
              accept="image/*"
              :loading="uploadingAudienceImage === i"
              @change="(e: Event) => handleAudienceImageUpload(e, i)"
            />
          </div>
          <div class="field--full flex items-center justify-between">
            <label class="featured-toggle">
              <Switch v-model="ex.featured" />
              Featured
            </label>
            <Button
              type="button"
              size="icon-sm"
              variant="ghost"
              class="text-destructive hover:text-destructive"
              @click="atr.audience_examples.splice(i, 1)"
            >
              <Icon name="mdi-delete-outline" size="16" />
            </Button>
          </div>
        </div>
      </div>
      <p v-if="!atr.audience_examples.length" class="nested-empty">No examples yet.</p>
      <Button
        size="sm"
        variant="secondary"
        @click="
          atr.audience_examples.push({ icon: 'mdi-store-outline', label: '', description: '', image_url: '', featured: false })
        "
      >
        <Icon name="mdi-plus" size="16" />
        Add example
      </Button>

      <h3 class="sub-heading">Personal Profile (all fields optional — hidden on the page when blank)</h3>
      <div class="field-grid">
        <div class="field">
          <Label>Photo URL</Label>
          <Input v-model="aboutData.profile_photo_url" />
        </div>
        <FileInput label="Upload photo" accept="image/*" :loading="uploadingPhoto" @change="handlePhotoUpload" />
        <div class="field">
          <Label>Greeting (e.g. Hi, I'm Nong.)</Label>
          <Input v-model="atr.profile_greeting" />
        </div>
        <div class="field">
          <Label>Name</Label>
          <Input v-model="atr.profile_name" />
        </div>
        <div class="field field--full">
          <Label>Bio</Label>
          <Textarea v-model="atr.profile_bio" rows="3" />
        </div>
        <div class="field">
          <Label>Contact email</Label>
          <Input v-model="aboutData.email" />
        </div>
      </div>

      <h4 class="sub-heading sub-heading--sm">Skills</h4>
      <div v-for="(skill, i) in atr.profile_skills" :key="i" class="repeat-row repeat-row--skill">
        <Input v-model="atr.profile_skills[i]" placeholder="Skill" />
        <Button size="icon-sm" variant="ghost" @click="atr.profile_skills.splice(i, 1)">
          <Icon name="mdi-close" size="16" />
        </Button>
      </div>
      <Button size="sm" variant="secondary" @click="atr.profile_skills.push('')">
        <Icon name="mdi-plus" size="16" />
        Add skill
      </Button>

      <h4 class="sub-heading sub-heading--sm">Social links (GitHub, LinkedIn, etc.)</h4>
      <p class="hint">
        Names "Telegram" / "Facebook" / "TikTok" / "GitHub" / "LinkedIn" get their matching icon automatically;
        anything else gets a generic link icon.
      </p>
      <div v-for="(s, i) in aboutData.socials" :key="i" class="repeat-row">
        <Input v-model="s.name" placeholder="Name" />
        <Input v-model="s.href" placeholder="URL" />
        <Button size="icon-sm" variant="ghost" @click="aboutData.socials.splice(i, 1)">
          <Icon name="mdi-close" size="16" />
        </Button>
      </div>
      <Button size="sm" variant="secondary" @click="aboutData.socials.push({ name: '', href: '' })">
        <Icon name="mdi-plus" size="16" />
        Add social link
      </Button>

      <h3 class="sub-heading">CTA</h3>
      <div class="field-grid">
        <div class="field field--full">
          <Label>Heading</Label>
          <Input v-model="atr.cta_title" />
        </div>
        <div class="field field--full">
          <Label>Description</Label>
          <Textarea v-model="atr.cta_description" rows="2" />
        </div>
        <div class="field">
          <Label>Primary CTA label</Label>
          <Input v-model="atr.cta_primary_label" />
        </div>
        <div class="field">
          <Label>Secondary CTA label</Label>
          <Input v-model="atr.cta_secondary_label" />
        </div>
      </div>

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
import { Switch } from '~/components/ui/switch'
import { Textarea } from '~/components/ui/textarea'
import { getAboutForEdit, updateAboutForLocale, DATA_KEYS } from '~/services/cms/siteContent'
import { uploadProductMedia } from '~/services/cms/adminProducts'
import { FALLBACK_ABOUT } from '~/stores/siteContent'

const props = defineProps<{
  activeLocale: string
  locales: { code: string, label: string }[]
}>()

const notify = useNotif()
const loading = ref(true)
const saving = ref(false)
const uploadingPhoto = ref(false)
const uploadingAudienceImage = ref<number | null>(null)
const error = ref<string | null>(null)

function splitFallback() {
  const dataKeys = DATA_KEYS.about ?? []
  const data: Record<string, any> = {}
  const content: Record<string, any> = {}
  for (const [k, v] of Object.entries(FALLBACK_ABOUT)) {
    if (k === 'id') continue
    ;(dataKeys.includes(k) ? data : content)[k] = v
  }
  return { data, content }
}
const FALLBACK = splitFallback()

function blankTr() {
  return {
    hero_tag: '',
    hero_heading: '',
    hero_description: '',
    hero_cta_primary_label: '',
    hero_cta_secondary_label: '',
    story_title: '',
    story_content: '',
    products_tag: '',
    products_title: '',
    products_description: '',
    approach_tag: '',
    approach_title: '',
    approach_cards: [] as { icon: string, title: string, description: string }[],
    audience_tag: '',
    audience_title: '',
    audience_description: '',
    audience_examples: [] as { icon: string, label: string, description: string, image_url: string, featured: boolean }[],
    profile_greeting: '',
    profile_name: '',
    profile_bio: '',
    profile_skills: [] as string[],
    cta_title: '',
    cta_description: '',
    cta_primary_label: '',
    cta_secondary_label: ''
  }
}

const aboutData = reactive<Record<string, any>>({
  email: '',
  profile_photo_url: '',
  socials: [] as { name: string, href: string }[],
  ...FALLBACK.data
})
const translationsByLocale = reactive<Record<string, ReturnType<typeof blankTr>>>(
  Object.fromEntries(props.locales.map((l) => [l.code, l.code === 'en' ? { ...blankTr(), ...FALLBACK.content } : blankTr()]))
)
const atr = computed(() => translationsByLocale[props.activeLocale]!)

onMounted(async () => {
  loading.value = true
  error.value = null
  try {
    const about = await getAboutForEdit()
    if (about) {
      Object.assign(aboutData, about.data)
      for (const loc of props.locales) {
        const found = about.translations.find((t) => t.locale === loc.code)
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

async function handlePhotoUpload(e: Event) {
  const file = (e.target as HTMLInputElement)?.files?.[0]
  if (!file) return
  uploadingPhoto.value = true
  try {
    aboutData.profile_photo_url = await uploadProductMedia(file)
  } catch (err: any) {
    notify(err.message || 'Failed to upload photo', { type: 'error' })
  } finally {
    uploadingPhoto.value = false
  }
}

async function handleAudienceImageUpload(e: Event, i: number) {
  const file = (e.target as HTMLInputElement)?.files?.[0]
  if (!file) return
  uploadingAudienceImage.value = i
  try {
    atr.value.audience_examples[i]!.image_url = await uploadProductMedia(file)
  } catch (err: any) {
    notify(err.message || 'Failed to upload image', { type: 'error' })
  } finally {
    uploadingAudienceImage.value = null
  }
}

async function save() {
  saving.value = true
  error.value = null
  try {
    await updateAboutForLocale(aboutData, atr.value, props.activeLocale)
    notify('About page saved', { type: 'success' })
  } catch (err: any) {
    notify(err.message || 'Failed to save About page', { type: 'error' })
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
.field-grid--thirds {
  grid-template-columns: repeat(3, 1fr);
}
.field--full {
  grid-column: 1 / -1;
}
.field--half {
  grid-column: span 2;
}
.field--quarter {
  grid-column: span 1;
}
@media (max-width: 640px) {
  .field-grid,
  .field-grid--thirds {
    grid-template-columns: 1fr;
  }
  .field--half {
    grid-column: 1 / -1;
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
.sub-heading--first {
  margin-top: 0;
}
.sub-heading--sm {
  font-size: 0.82rem;
  margin: 18px 0 10px;
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
.repeat-row--wide {
  grid-template-columns: 1fr 1fr 1.4fr auto;
}
.repeat-row--skill {
  grid-template-columns: 1fr auto;
}
@media (max-width: 640px) {
  .repeat-row,
  .repeat-row--wide,
  .repeat-row--skill {
    grid-template-columns: 1fr;
  }
}
.featured-toggle {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 0.85rem;
  white-space: nowrap;
}

.nested-card {
  padding: 14px 16px;
  border: 1px solid color-mix(in srgb, var(--foreground) 7%, transparent);
  border-radius: var(--radius-sm);
  margin-bottom: 10px;
}
.nested-card--shot {
  display: flex;
  gap: 14px;
  align-items: flex-start;
}
.shot-preview {
  width: 96px;
  height: 64px;
  object-fit: cover;
  border-radius: var(--radius-sm);
  flex-shrink: 0;
}
.nested-empty {
  font-size: 0.85rem;
  color: color-mix(in srgb, var(--foreground) 50%, transparent);
  margin: 0 0 10px;
}

.save-row {
  margin-top: 24px;
}
</style>
