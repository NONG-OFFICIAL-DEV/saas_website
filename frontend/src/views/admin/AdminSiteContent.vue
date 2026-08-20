<template>
  <div>
    <h1 class="page-title">Site Content</h1>
    <p class="page-sub">Edit the homepage hero, About page, and footer.</p>

    <v-alert v-if="error" type="error" variant="tonal" rounded="lg" class="mb-4">
      {{ error }}
    </v-alert>
    <v-alert v-if="savedNotice" type="success" variant="tonal" rounded="lg" class="mb-4">
      Saved.
    </v-alert>

    <div v-if="loading" class="page-loading">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <v-tabs v-else v-model="tab" class="mb-6">
      <v-tab value="hero">Hero</v-tab>
      <v-tab value="about">About</v-tab>
      <v-tab value="footer">Footer</v-tab>
    </v-tabs>

    <v-window v-if="!loading" v-model="tab">
      <!-- ── Hero ── -->
      <v-window-item value="hero">
        <section class="editor-section">
          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field v-model="hero.badge_text" label="Badge text" />
            </v-col>
            <v-col cols="12" sm="6" />
            <v-col cols="12" sm="6">
              <v-text-field v-model="hero.headline" label="Headline" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="hero.subheadline" label="Subheadline" />
            </v-col>
            <v-col cols="12">
              <v-textarea v-model="hero.description" label="Description" rows="2" auto-grow />
            </v-col>
            <v-col cols="12">
              <v-text-field v-model="hero.trust_line" label="Trust line" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="hero.cta_primary_label" label="Primary CTA label" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="hero.cta_secondary_label" label="Secondary CTA label" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="hero.cta_secondary_url" label="Secondary CTA URL" />
            </v-col>
          </v-row>

          <h3 class="sub-heading">Stats</h3>
          <div v-for="(s, i) in hero.stats" :key="i" class="repeat-row">
            <v-text-field v-model="s.num" label="Value" density="compact" />
            <v-text-field v-model="s.label" label="Label" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="hero.stats.splice(i, 1)" />
          </div>
          <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="hero.stats.push({ num: '', label: '' })">
            Add stat
          </v-btn>

          <div class="save-row">
            <v-btn color="primary" variant="flat" rounded="lg" :loading="saving" @click="saveHero">Save</v-btn>
          </div>
        </section>
      </v-window-item>

      <!-- ── About ── -->
      <v-window-item value="about">
        <section class="editor-section">
          <h3 class="sub-heading sub-heading--first">Hero</h3>
          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.hero_tag" label="Eyebrow tag" />
            </v-col>
            <v-col cols="12" sm="6" />
            <v-col cols="12">
              <v-text-field v-model="about.hero_heading" label="Heading" />
            </v-col>
            <v-col cols="12">
              <v-textarea v-model="about.hero_description" label="Description" rows="2" auto-grow />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.hero_cta_primary_label" label="Primary CTA label" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.hero_cta_secondary_label" label="Secondary CTA label" />
            </v-col>
          </v-row>

          <h3 class="sub-heading">Story ("Why I build these products")</h3>
          <v-row dense>
            <v-col cols="12">
              <v-text-field v-model="about.story_title" label="Title" />
            </v-col>
            <v-col cols="12">
              <v-textarea
                v-model="about.story_content"
                label="Content (separate paragraphs with a blank line)"
                rows="4"
                auto-grow
              />
            </v-col>
          </v-row>

          <h3 class="sub-heading">What I Build (section intro — products themselves come from the Products page)</h3>
          <v-row dense>
            <v-col cols="12" sm="4">
              <v-text-field v-model="about.products_tag" label="Eyebrow tag" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="about.products_title" label="Title" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="about.products_description" label="Description" />
            </v-col>
          </v-row>

          <h3 class="sub-heading">Approach ("How I build" — 4 cards)</h3>
          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.approach_tag" label="Eyebrow tag" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.approach_title" label="Title" />
            </v-col>
          </v-row>
          <div v-for="(c, i) in about.approach_cards" :key="i" class="repeat-row repeat-row--wide">
            <v-text-field v-model="c.icon" label="Icon (mdi-...)" density="compact" />
            <v-text-field v-model="c.title" label="Title" density="compact" />
            <v-text-field v-model="c.description" label="Description" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="about.approach_cards.splice(i, 1)" />
          </div>
          <v-btn
            size="small"
            variant="tonal"
            prepend-icon="mdi-plus"
            @click="about.approach_cards.push({ icon: 'mdi-check-circle-outline', title: '', description: '' })"
          >
            Add card
          </v-btn>

          <h3 class="sub-heading">Who I Build For</h3>
          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.audience_title" label="Title" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.audience_description" label="Description" />
            </v-col>
          </v-row>
          <div v-for="(ex, i) in about.audience_examples" :key="i" class="repeat-row repeat-row--audience">
            <v-text-field v-model="ex.label" label="Label" density="compact" />
            <v-text-field v-model="ex.icon" label="Fallback icon (mdi-...)" density="compact" />
            <v-text-field v-model="ex.image_url" label="Image URL" density="compact" />
            <v-file-input
              label="Upload image"
              accept="image/*"
              prepend-icon=""
              density="compact"
              :loading="uploadingAudienceImage === i"
              @change="e => handleAudienceImageUpload(e, i)"
            />
            <v-btn icon="mdi-close" size="small" variant="text" @click="about.audience_examples.splice(i, 1)" />
          </div>
          <v-btn
            size="small"
            variant="tonal"
            prepend-icon="mdi-plus"
            @click="about.audience_examples.push({ icon: 'mdi-store-outline', label: '', image_url: '' })"
          >
            Add example
          </v-btn>

          <h3 class="sub-heading">Personal Profile (all fields optional — hidden on the page when blank)</h3>
          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.profile_photo_url" label="Photo URL" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-file-input
                label="Upload photo"
                accept="image/*"
                prepend-icon=""
                :loading="uploadingPhoto"
                @change="handlePhotoUpload"
              />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.profile_greeting" label="Greeting (e.g. Hi, I'm Nong.)" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.profile_name" label="Name" />
            </v-col>
            <v-col cols="12">
              <v-textarea v-model="about.profile_bio" label="Bio" rows="3" auto-grow />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.email" label="Contact email" />
            </v-col>
          </v-row>

          <h4 class="sub-heading sub-heading--sm">Skills</h4>
          <div v-for="(skill, i) in about.profile_skills" :key="i" class="repeat-row repeat-row--skill">
            <v-text-field v-model="about.profile_skills[i]" label="Skill" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="about.profile_skills.splice(i, 1)" />
          </div>
          <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="about.profile_skills.push('')">
            Add skill
          </v-btn>

          <h4 class="sub-heading sub-heading--sm">Social links (GitHub, LinkedIn, etc.)</h4>
          <p class="hint">
            Names "Telegram" / "Facebook" / "TikTok" / "GitHub" / "LinkedIn" get their matching icon automatically;
            anything else gets a generic link icon.
          </p>
          <div v-for="(s, i) in about.socials" :key="i" class="repeat-row">
            <v-text-field v-model="s.name" label="Name" density="compact" />
            <v-text-field v-model="s.href" label="URL" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="about.socials.splice(i, 1)" />
          </div>
          <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="about.socials.push({ name: '', href: '' })">
            Add social link
          </v-btn>

          <h3 class="sub-heading">CTA</h3>
          <v-row dense>
            <v-col cols="12">
              <v-text-field v-model="about.cta_title" label="Heading" />
            </v-col>
            <v-col cols="12">
              <v-textarea v-model="about.cta_description" label="Description" rows="2" auto-grow />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.cta_primary_label" label="Primary CTA label" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.cta_secondary_label" label="Secondary CTA label" />
            </v-col>
          </v-row>

          <div class="save-row">
            <v-btn color="primary" variant="flat" rounded="lg" :loading="saving" @click="saveAbout">Save</v-btn>
          </div>
        </section>
      </v-window-item>

      <!-- ── Footer ── -->
      <v-window-item value="footer">
        <section class="editor-section">
          <v-row dense>
            <v-col cols="12" sm="4">
              <v-text-field v-model="footer.email" label="Email" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="footer.phone" label="Phone" />
            </v-col>
            <v-col cols="12" sm="4">
              <v-text-field v-model="footer.address" label="Address" />
            </v-col>
          </v-row>

          <h3 class="sub-heading">Social links</h3>
          <p class="hint">Names "Telegram" / "Facebook" / "TikTok" get their matching icon automatically; anything else gets a generic link icon.</p>
          <div v-for="(s, i) in footer.socials" :key="i" class="repeat-row">
            <v-text-field v-model="s.name" label="Name" density="compact" />
            <v-text-field v-model="s.href" label="URL" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="footer.socials.splice(i, 1)" />
          </div>
          <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="footer.socials.push({ name: '', href: '' })">
            Add social link
          </v-btn>

          <div class="save-row">
            <v-btn color="primary" variant="flat" rounded="lg" :loading="saving" @click="saveFooter">Save</v-btn>
          </div>
        </section>
      </v-window-item>
    </v-window>
  </div>
</template>

<script setup>
  import { onMounted, ref } from 'vue'
  import { getHero, updateHero, getAbout, updateAbout, getFooter, updateFooter } from '@/services/siteContent'
  import { uploadProductMedia } from '@/services/adminProducts'

  const tab = ref('hero')
  const loading = ref(true)
  const saving = ref(false)
  const uploadingPhoto = ref(false)
  const uploadingAudienceImage = ref(null)
  const error = ref(null)
  const savedNotice = ref(false)

  const hero = ref({ stats: [] })
  const about = ref({ approach_cards: [], audience_examples: [], profile_skills: [], socials: [] })
  const footer = ref({ socials: [] })

  function flashSaved() {
    savedNotice.value = true
    setTimeout(() => (savedNotice.value = false), 2000)
  }

  onMounted(async () => {
    loading.value = true
    error.value = null
    try {
      const [heroData, aboutData, footerData] = await Promise.all([getHero(), getAbout(), getFooter()])
      if (heroData) hero.value = heroData
      // Merge over the default shape so a not-yet-migrated CMS row (missing
      // newer array fields) doesn't blow up the repeaters below with undefined.
      if (aboutData) about.value = { approach_cards: [], audience_examples: [], profile_skills: [], socials: [], ...aboutData }
      if (footerData) footer.value = footerData
    } catch (err) {
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
      flashSaved()
    } catch (err) {
      error.value = err.message
    } finally {
      saving.value = false
    }
  }

  async function handlePhotoUpload(e) {
    const file = e?.target?.files?.[0] ?? e?.[0]
    if (!file) return
    uploadingPhoto.value = true
    try {
      about.value.profile_photo_url = await uploadProductMedia(file)
    } catch (err) {
      error.value = err.message
    } finally {
      uploadingPhoto.value = false
    }
  }

  async function handleAudienceImageUpload(e, i) {
    const file = e?.target?.files?.[0] ?? e?.[0]
    if (!file) return
    uploadingAudienceImage.value = i
    try {
      about.value.audience_examples[i].image_url = await uploadProductMedia(file)
    } catch (err) {
      error.value = err.message
    } finally {
      uploadingAudienceImage.value = null
    }
  }

  async function saveAbout() {
    saving.value = true
    error.value = null
    try {
      await updateAbout(about.value.id, about.value)
      flashSaved()
    } catch (err) {
      error.value = err.message
    } finally {
      saving.value = false
    }
  }

  async function saveFooter() {
    saving.value = true
    error.value = null
    try {
      await updateFooter(footer.value.id, footer.value)
      flashSaved()
    } catch (err) {
      error.value = err.message
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
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin: 0 0 24px;
  }
  .page-loading {
    display: flex;
    justify-content: center;
    padding: 60px 0;
  }

  .editor-section {
    padding: 24px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    border-radius: 14px;
    background: rgba(var(--v-theme-surface), 0.6);
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
    color: rgba(var(--v-theme-on-surface), 0.55);
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
  .repeat-row--audience {
    grid-template-columns: 1fr 1fr 1fr 1fr auto;
  }

  .save-row {
    margin-top: 24px;
  }

  @media (max-width: 640px) {
    .repeat-row,
    .repeat-row--wide,
    .repeat-row--skill,
    .repeat-row--audience {
      grid-template-columns: 1fr;
    }
  }
</style>
