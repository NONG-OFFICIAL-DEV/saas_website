<template>
  <div>
    <h1 class="page-title">Site Content</h1>
    <p class="page-sub">Edit the homepage hero, About page, and footer.</p>

    <Alert v-if="error" variant="destructive" class="mb-4">
      <AlertDescription>{{ error }}</AlertDescription>
    </Alert>

    <InlineLoader v-if="loading" min-height="120px" />

    <Tabs v-else v-model="tab" class="w-full">
      <TabsList class="mb-6">
        <TabsTrigger value="hero">Hero</TabsTrigger>
        <TabsTrigger value="about">About</TabsTrigger>
        <TabsTrigger value="footer">Footer</TabsTrigger>
      </TabsList>

      <!-- ── Hero ── -->
      <TabsContent value="hero">
        <section class="editor-section">
          <Row dense>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Badge text</Label>
                <Input v-model="hero.badge_text" />
              </div>
            </Col>
            <Col cols="12" sm="6" />
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Headline</Label>
                <Input v-model="hero.headline" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Subheadline</Label>
                <Input v-model="hero.subheadline" />
              </div>
            </Col>
            <Col cols="12">
              <div class="field">
                <Label>Description</Label>
                <Textarea v-model="hero.description" rows="2" />
              </div>
            </Col>
            <Col cols="12">
              <div class="field">
                <Label>Trust line</Label>
                <Input v-model="hero.trust_line" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Primary CTA label</Label>
                <Input v-model="hero.cta_primary_label" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Secondary CTA label</Label>
                <Input v-model="hero.cta_secondary_label" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Secondary CTA URL</Label>
                <Input v-model="hero.cta_secondary_url" />
              </div>
            </Col>
          </Row>

          <h3 class="sub-heading">Stats</h3>
          <div v-for="(s, i) in hero.stats" :key="i" class="repeat-row">
            <Input v-model="s.num" placeholder="Value" />
            <Input v-model="s.label" placeholder="Label" />
            <Button size="icon-sm" variant="ghost" @click="hero.stats.splice(i, 1)">
              <Icon name="mdi-close" size="16" />
            </Button>
          </div>
          <Button size="sm" variant="secondary" @click="hero.stats.push({ num: '', label: '' })">
            <Icon name="mdi-plus" size="16" />
            Add stat
          </Button>

          <div class="save-row">
            <Button :disabled="saving" @click="saveHero">
              <Icon v-if="saving" name="mdi-loading" size="16" class="animate-spin" />
              Save
            </Button>
          </div>
        </section>
      </TabsContent>

      <!-- ── About ── -->
      <TabsContent value="about">
        <section class="editor-section">
          <h3 class="sub-heading sub-heading--first">Hero</h3>
          <Row dense>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Eyebrow tag</Label>
                <Input v-model="about.hero_tag" />
              </div>
            </Col>
            <Col cols="12" sm="6" />
            <Col cols="12">
              <div class="field">
                <Label>Heading</Label>
                <Input v-model="about.hero_heading" />
              </div>
            </Col>
            <Col cols="12">
              <div class="field">
                <Label>Description</Label>
                <Textarea v-model="about.hero_description" rows="2" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Primary CTA label</Label>
                <Input v-model="about.hero_cta_primary_label" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Secondary CTA label</Label>
                <Input v-model="about.hero_cta_secondary_label" />
              </div>
            </Col>
          </Row>

          <h3 class="sub-heading">Story ("Why I build these products")</h3>
          <Row dense>
            <Col cols="12">
              <div class="field">
                <Label>Title</Label>
                <Input v-model="about.story_title" />
              </div>
            </Col>
            <Col cols="12">
              <div class="field">
                <Label>Content (separate paragraphs with a blank line)</Label>
                <Textarea v-model="about.story_content" rows="4" />
              </div>
            </Col>
          </Row>

          <h3 class="sub-heading">What I Build (section intro — products themselves come from the Products page)</h3>
          <Row dense>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Eyebrow tag</Label>
                <Input v-model="about.products_tag" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Title</Label>
                <Input v-model="about.products_title" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Description</Label>
                <Input v-model="about.products_description" />
              </div>
            </Col>
          </Row>

          <h3 class="sub-heading">Approach ("How I build" — 4 cards)</h3>
          <Row dense>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Eyebrow tag</Label>
                <Input v-model="about.approach_tag" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Title</Label>
                <Input v-model="about.approach_title" />
              </div>
            </Col>
          </Row>
          <div v-for="(c, i) in about.approach_cards" :key="i" class="repeat-row repeat-row--wide">
            <Input v-model="c.icon" placeholder="Icon (mdi-...)" />
            <Input v-model="c.title" placeholder="Title" />
            <Input v-model="c.description" placeholder="Description" />
            <Button size="icon-sm" variant="ghost" @click="about.approach_cards.splice(i, 1)">
              <Icon name="mdi-close" size="16" />
            </Button>
          </div>
          <Button
            size="sm"
            variant="secondary"
            @click="about.approach_cards.push({ icon: 'mdi-check-circle-outline', title: '', description: '' })"
          >
            <Icon name="mdi-plus" size="16" />
            Add card
          </Button>

          <h3 class="sub-heading">Who I Build For</h3>
          <Row dense>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Title</Label>
                <Input v-model="about.audience_title" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Description</Label>
                <Input v-model="about.audience_description" />
              </div>
            </Col>
          </Row>
          <div v-for="(ex, i) in about.audience_examples" :key="i" class="nested-card nested-card--shot">
            <img v-if="ex.image_url" :src="ex.image_url" class="shot-preview" :alt="ex.label" />
            <Row dense class="grow">
              <Col cols="12" sm="6" md="3">
                <div class="field">
                  <Label>Label</Label>
                  <Input v-model="ex.label" />
                </div>
              </Col>
              <Col cols="12" sm="6" md="3">
                <div class="field">
                  <Label>Fallback icon (mdi-...)</Label>
                  <Input v-model="ex.icon" />
                </div>
              </Col>
              <Col cols="12" md="6">
                <div class="field">
                  <Label>Description</Label>
                  <Input v-model="ex.description" />
                </div>
              </Col>
              <Col cols="12" sm="6">
                <div class="field">
                  <Label>Image URL</Label>
                  <Input v-model="ex.image_url" placeholder="https://..." />
                </div>
              </Col>
              <Col cols="12" sm="6">
                <div class="field">
                  <Label>Or upload an image</Label>
                  <FileInput
                    accept="image/*"
                    :loading="uploadingAudienceImage === i"
                    @change="(e: Event) => handleAudienceImageUpload(e, i)"
                  />
                </div>
              </Col>
              <Col cols="12" class="flex items-center justify-between">
                <label class="featured-toggle">
                  <Switch v-model="ex.featured" />
                  Featured
                </label>
                <Button
                  type="button"
                  size="icon-sm"
                  variant="ghost"
                  class="text-destructive hover:text-destructive"
                  @click="about.audience_examples.splice(i, 1)"
                >
                  <Icon name="mdi-delete-outline" size="16" />
                </Button>
              </Col>
            </Row>
          </div>
          <p v-if="!about.audience_examples.length" class="nested-empty">No examples yet.</p>
          <Button
            size="sm"
            variant="secondary"
            @click="
              about.audience_examples.push({ icon: 'mdi-store-outline', label: '', description: '', image_url: '', featured: false })
            "
          >
            <Icon name="mdi-plus" size="16" />
            Add example
          </Button>

          <h3 class="sub-heading">Personal Profile (all fields optional — hidden on the page when blank)</h3>
          <Row dense>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Photo URL</Label>
                <Input v-model="about.profile_photo_url" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <FileInput label="Upload photo" accept="image/*" :loading="uploadingPhoto" @change="handlePhotoUpload" />
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Greeting (e.g. Hi, I'm Nong.)</Label>
                <Input v-model="about.profile_greeting" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Name</Label>
                <Input v-model="about.profile_name" />
              </div>
            </Col>
            <Col cols="12">
              <div class="field">
                <Label>Bio</Label>
                <Textarea v-model="about.profile_bio" rows="3" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Contact email</Label>
                <Input v-model="about.email" />
              </div>
            </Col>
          </Row>

          <h4 class="sub-heading sub-heading--sm">Skills</h4>
          <div v-for="(skill, i) in about.profile_skills" :key="i" class="repeat-row repeat-row--skill">
            <Input v-model="about.profile_skills[i]" placeholder="Skill" />
            <Button size="icon-sm" variant="ghost" @click="about.profile_skills.splice(i, 1)">
              <Icon name="mdi-close" size="16" />
            </Button>
          </div>
          <Button size="sm" variant="secondary" @click="about.profile_skills.push('')">
            <Icon name="mdi-plus" size="16" />
            Add skill
          </Button>

          <h4 class="sub-heading sub-heading--sm">Social links (GitHub, LinkedIn, etc.)</h4>
          <p class="hint">
            Names "Telegram" / "Facebook" / "TikTok" / "GitHub" / "LinkedIn" get their matching icon automatically;
            anything else gets a generic link icon.
          </p>
          <div v-for="(s, i) in about.socials" :key="i" class="repeat-row">
            <Input v-model="s.name" placeholder="Name" />
            <Input v-model="s.href" placeholder="URL" />
            <Button size="icon-sm" variant="ghost" @click="about.socials.splice(i, 1)">
              <Icon name="mdi-close" size="16" />
            </Button>
          </div>
          <Button size="sm" variant="secondary" @click="about.socials.push({ name: '', href: '' })">
            <Icon name="mdi-plus" size="16" />
            Add social link
          </Button>

          <h3 class="sub-heading">CTA</h3>
          <Row dense>
            <Col cols="12">
              <div class="field">
                <Label>Heading</Label>
                <Input v-model="about.cta_title" />
              </div>
            </Col>
            <Col cols="12">
              <div class="field">
                <Label>Description</Label>
                <Textarea v-model="about.cta_description" rows="2" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Primary CTA label</Label>
                <Input v-model="about.cta_primary_label" />
              </div>
            </Col>
            <Col cols="12" sm="6">
              <div class="field">
                <Label>Secondary CTA label</Label>
                <Input v-model="about.cta_secondary_label" />
              </div>
            </Col>
          </Row>

          <div class="save-row">
            <Button :disabled="saving" @click="saveAbout">
              <Icon v-if="saving" name="mdi-loading" size="16" class="animate-spin" />
              Save
            </Button>
          </div>
        </section>
      </TabsContent>

      <!-- ── Footer ── -->
      <TabsContent value="footer">
        <section class="editor-section">
          <Row dense>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Email</Label>
                <Input v-model="footer.email" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Phone</Label>
                <Input v-model="footer.phone" />
              </div>
            </Col>
            <Col cols="12" sm="4">
              <div class="field">
                <Label>Address</Label>
                <Input v-model="footer.address" />
              </div>
            </Col>
          </Row>

          <h3 class="sub-heading">Social links</h3>
          <p class="hint">Names "Telegram" / "Facebook" / "TikTok" get their matching icon automatically; anything else gets a generic link icon.</p>
          <div v-for="(s, i) in footer.socials" :key="i" class="repeat-row">
            <Input v-model="s.name" placeholder="Name" />
            <Input v-model="s.href" placeholder="URL" />
            <Button size="icon-sm" variant="ghost" @click="footer.socials.splice(i, 1)">
              <Icon name="mdi-close" size="16" />
            </Button>
          </div>
          <Button size="sm" variant="secondary" @click="footer.socials.push({ name: '', href: '' })">
            <Icon name="mdi-plus" size="16" />
            Add social link
          </Button>

          <div class="save-row">
            <Button :disabled="saving" @click="saveFooter">
              <Icon v-if="saving" name="mdi-loading" size="16" class="animate-spin" />
              Save
            </Button>
          </div>
        </section>
      </TabsContent>
    </Tabs>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({ layout: 'admin' })

  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Button } from '~/components/ui/button'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'
  import { Switch } from '~/components/ui/switch'
  import { Tabs, TabsContent, TabsList, TabsTrigger } from '~/components/ui/tabs'
  import { Textarea } from '~/components/ui/textarea'
  import { getHero, updateHero, getAbout, updateAbout, getFooter, updateFooter } from '~/services/cms/siteContent'
  import { uploadProductMedia } from '~/services/cms/adminProducts'
  import { FALLBACK_HERO, FALLBACK_ABOUT, FALLBACK_FOOTER } from '~/stores/siteContent'
  import type { HeroContent, AboutContent, FooterContent } from '~/types'

  // This editor always keeps these array fields populated (seeded below,
  // merged-over-defaults on load) — narrowed locally so the repeater
  // templates below don't need an assertion at every access site.
  type EditableHero = HeroContent & { stats: { num: string; label: string }[] }
  type EditableAbout = AboutContent & {
    approach_cards: { icon: string; title: string; description: string }[]
    audience_examples: { icon: string; label: string; description: string; image_url: string; featured: boolean }[]
    profile_skills: string[]
    socials: { name: string; href: string }[]
  }
  type EditableFooter = FooterContent & { socials: { name: string; href: string }[] }

  const notify = useNotif()
  const tab = ref('hero')
  const loading = ref(true)
  const saving = ref(false)
  const uploadingPhoto = ref(false)
  const uploadingAudienceImage = ref<number | null>(null)
  const error = ref<string | null>(null)

  const hero = ref<EditableHero>({ ...FALLBACK_HERO } as EditableHero)
  const about = ref<EditableAbout>({ ...FALLBACK_ABOUT } as EditableAbout)
  const footer = ref<EditableFooter>({ ...FALLBACK_FOOTER } as EditableFooter)

  onMounted(async () => {
    loading.value = true
    error.value = null
    try {
      const [heroData, aboutData, footerData] = await Promise.all([getHero(), getAbout(), getFooter()])
      // Merge over the same fallback content the public site shows, rather
      // than replacing outright — a CMS row that hasn't been re-saved since
      // a content-shape change (or was never seeded past very old defaults)
      // still has real starting values to edit instead of blank fields.
      if (heroData) hero.value = { ...FALLBACK_HERO, ...heroData } as EditableHero
      if (aboutData) about.value = { ...FALLBACK_ABOUT, ...aboutData } as EditableAbout
      if (footerData) footer.value = { ...FALLBACK_FOOTER, ...footerData } as EditableFooter
    } catch (err: any) {
      error.value = err.message
    } finally {
      loading.value = false
    }
  })

  async function saveHero() {
    saving.value = true
    error.value = null
    try {
      await updateHero(hero.value.id, hero.value)
      notify('Hero section saved', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save hero section', { type: 'error' })
    } finally {
      saving.value = false
    }
  }

  async function handlePhotoUpload(e: Event) {
    const file = (e.target as HTMLInputElement)?.files?.[0]
    if (!file) return
    uploadingPhoto.value = true
    try {
      about.value.profile_photo_url = await uploadProductMedia(file)
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
      ;(about.value.audience_examples as any[])[i].image_url = await uploadProductMedia(file)
    } catch (err: any) {
      notify(err.message || 'Failed to upload image', { type: 'error' })
    } finally {
      uploadingAudienceImage.value = null
    }
  }

  async function saveAbout() {
    saving.value = true
    error.value = null
    try {
      await updateAbout(about.value.id, about.value)
      notify('About page saved', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save About page', { type: 'error' })
    } finally {
      saving.value = false
    }
  }

  async function saveFooter() {
    saving.value = true
    error.value = null
    try {
      await updateFooter(footer.value.id, footer.value)
      notify('Footer saved', { type: 'success' })
    } catch (err: any) {
      notify(err.message || 'Failed to save footer', { type: 'error' })
    } finally {
      saving.value = false
    }
  }
</script>

<style scoped>
  .page-title {
    font-size: 1.5rem;
    font-weight: 800;
    margin: 0 0 4px;
  }
  .page-sub {
    font-size: 0.86rem;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    margin: 0 0 24px;
  }
  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .field-error {
    font-size: 0.75rem;
    color: var(--destructive);
    margin: 0;
  }

  .editor-section {
    padding: 24px;
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    border-radius: 14px;
    background: color-mix(in srgb, var(--card) 60%, transparent);
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
    border-radius: 10px;
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
    border-radius: 8px;
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

  @media (max-width: 640px) {
    .repeat-row,
    .repeat-row--wide,
    .repeat-row--skill {
      grid-template-columns: 1fr;
    }
  }
</style>
