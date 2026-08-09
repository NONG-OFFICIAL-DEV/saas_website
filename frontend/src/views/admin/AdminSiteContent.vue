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
          <v-row dense>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.tag" label="Eyebrow tag" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.greeting" label="Greeting" />
            </v-col>
            <v-col cols="12">
              <v-textarea v-model="about.bio" label="Bio" rows="3" auto-grow />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.email" label="Contact email" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.cta_label" label="CTA label" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.how_tag" label='"How I work" eyebrow tag' />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.how_title" label='"How I work" title' />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.talk_title" label="Let's talk: title" />
            </v-col>
            <v-col cols="12" sm="6">
              <v-text-field v-model="about.talk_description" label="Let's talk: description" />
            </v-col>
          </v-row>

          <h3 class="sub-heading">Values (the 3 numbered cards)</h3>
          <div v-for="(v, i) in about.values" :key="i" class="repeat-row repeat-row--wide">
            <v-text-field v-model="v.icon" label="Icon (mdi-...)" density="compact" />
            <v-text-field v-model="v.title" label="Title" density="compact" />
            <v-text-field v-model="v.description" label="Description" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="about.values.splice(i, 1)" />
          </div>
          <v-btn
            size="small"
            variant="tonal"
            prepend-icon="mdi-plus"
            @click="about.values.push({ icon: 'mdi-check-circle-outline', title: '', description: '' })"
          >
            Add value
          </v-btn>

          <h3 class="sub-heading">Social links</h3>
          <div v-for="(s, i) in about.socials" :key="i" class="repeat-row">
            <v-text-field v-model="s.name" label="Name" density="compact" />
            <v-text-field v-model="s.href" label="URL" density="compact" />
            <v-btn icon="mdi-close" size="small" variant="text" @click="about.socials.splice(i, 1)" />
          </div>
          <v-btn size="small" variant="tonal" prepend-icon="mdi-plus" @click="about.socials.push({ name: '', href: '' })">
            Add social link
          </v-btn>

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

  const tab = ref('hero')
  const loading = ref(true)
  const saving = ref(false)
  const error = ref(null)
  const savedNotice = ref(false)

  const hero = ref({ stats: [] })
  const about = ref({ values: [], socials: [] })
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
      if (aboutData) about.value = aboutData
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

  .save-row {
    margin-top: 24px;
  }

  @media (max-width: 640px) {
    .repeat-row,
    .repeat-row--wide {
      grid-template-columns: 1fr;
    }
  }
</style>
