<template>
  <div class="about-page" :style="pageVars">
    <!-- ── Hero ─────────────────────────────────────────────────────────── -->
    <section class="section-pad hero-section">
      <div class="hero-decor d-none d-md-block" aria-hidden="true">
        <Geometric3D />
      </div>
      <v-container>
        <div class="hero-content" data-aos="fade-up">
          <span class="section-tag">{{ about.hero_tag }}</span>
          <h1 class="section-title">{{ about.hero_heading }}</h1>
          <p class="section-sub hero-description mx-auto">{{ about.hero_description }}</p>

          <div class="hero-links">
            <v-btn color="primary" variant="flat" to="/products" append-icon="mdi-arrow-right">
              {{ about.hero_cta_primary_label }}
            </v-btn>
            <v-btn variant="outlined" href="/#contact" prepend-icon="mdi-email-outline">
              {{ about.hero_cta_secondary_label }}
            </v-btn>
          </div>
        </div>
      </v-container>
    </section>

    <!-- ── My Story ─────────────────────────────────────────────────────── -->
    <section class="section-pad section-tint-neutral">
      <v-container>
        <div class="story-block" data-aos="fade-up">
          <h2 class="section-title">{{ about.story_title }}</h2>
          <p v-for="(para, i) in storyParagraphs" :key="i" class="section-sub story-para">
            {{ para }}
          </p>
        </div>
      </v-container>
    </section>

    <!-- ── What I Build ─────────────────────────────────────────────────── -->
    <section class="section-pad section-tint-sky">
      <v-container>
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="section-tag">{{ about.products_tag }}</span>
          <h2 class="section-title">{{ about.products_title }}</h2>
          <p class="section-sub products-sub mx-auto">{{ about.products_description }}</p>
        </div>

        <InlineLoader v-if="productsStore.loading" min-height="200px" />
        <div v-else class="products-grid" data-aos="fade-up">
          <ProductCard v-for="product in productsStore.products" :key="product.id" :product="product" />
        </div>
      </v-container>
    </section>

    <!-- ── My Approach ──────────────────────────────────────────────────── -->
    <section class="section-pad">
      <v-container>
        <div class="text-center mb-10" data-aos="fade-up">
          <span class="section-tag">{{ about.approach_tag }}</span>
          <h2 class="section-title">{{ about.approach_title }}</h2>
        </div>

        <div class="values-grid">
          <template v-for="(v, i) in about.approach_cards ?? []" :key="v.title">
            <div class="value-card" data-aos="fade-up" :data-aos-delay="i * 100">
              <span class="value-num">{{ String(i + 1).padStart(2, '0') }}</span>
              <v-icon :icon="v.icon" size="24" color="primary" />
              <h3>{{ v.title }}</h3>
              <p>{{ v.description }}</p>
            </div>
            <div v-if="i < (about.approach_cards?.length ?? 0) - 1" class="value-connector" aria-hidden="true" />
          </template>
        </div>
      </v-container>
    </section>

    <!-- ── Who I Build For ──────────────────────────────────────────────── -->
    <section class="section-pad section-tint-peach">
      <v-container class="text-center">
        <div class="mb-8" data-aos="fade-up">
          <h2 class="section-title">{{ about.audience_title }}</h2>
          <p class="section-sub audience-sub mx-auto">{{ about.audience_description }}</p>
        </div>

        <div class="audience-grid" data-aos="fade-up">
          <div
            v-for="ex in about.audience_examples ?? []"
            :key="ex.label"
            class="audience-card clay-surface clay-surface--interactive"
          >
            <div class="audience-image">
              <v-img v-if="ex.image_url" :src="ex.image_url" :alt="ex.label" cover class="audience-photo" />
              <v-icon v-else :icon="ex.icon" size="40" />
            </div>
            <span class="audience-label">{{ ex.label }}</span>
          </div>
        </div>
      </v-container>
    </section>

    <!-- ── Personal Profile ─────────────────────────────────────────────── -->
    <section class="section-pad">
      <v-container>
        <v-row align="center">
          <v-col cols="12" md="4" class="text-center" data-aos="fade-right">
            <v-avatar size="180" color="primary" class="profile-avatar">
              <v-img v-if="about.profile_photo_url" :src="about.profile_photo_url" :alt="about.profile_name" cover />
              <v-icon v-else icon="mdi-account" size="90" color="white" />
            </v-avatar>
          </v-col>

          <v-col cols="12" md="8" class="text-center text-md-left" data-aos="fade-left">
            <p v-if="about.profile_greeting" class="profile-greeting">{{ about.profile_greeting }}</p>
            <h2 v-if="about.profile_name" class="section-title profile-name">{{ about.profile_name }}</h2>
            <p v-if="about.profile_bio" class="section-sub about-lead">{{ about.profile_bio }}</p>

            <div v-if="about.profile_skills?.length" class="skills-row">
              <v-chip v-for="skill in about.profile_skills" :key="skill" size="small" variant="tonal" color="#8B5CF6">
                {{ skill }}
              </v-chip>
            </div>

            <div v-if="about.socials?.length" class="social-row">
              <a
                v-for="social in about.socials"
                :key="social.name"
                :href="social.href"
                :aria-label="social.name"
                class="social-btn"
                target="_blank"
                rel="noopener"
              >
                <SocialIcon :name="social.name.toLowerCase()" />
              </a>
            </div>
          </v-col>
        </v-row>
      </v-container>
    </section>

    <!-- ── CTA ──────────────────────────────────────────────────────────── -->
    <section class="section-pad section-tint-lavender">
      <v-container class="text-center">
        <h2 class="section-title" data-aos="fade-up">{{ about.cta_title }}</h2>
        <p class="section-sub about-lead mx-auto" data-aos="fade-up">
          {{ about.cta_description }}
        </p>
        <div class="cta-links" data-aos="fade-up">
          <v-btn color="primary" variant="flat" href="/#contact" append-icon="mdi-arrow-right">
            {{ about.cta_primary_label }}
          </v-btn>
          <v-btn variant="outlined" to="/products">
            {{ about.cta_secondary_label }}
          </v-btn>
        </div>
      </v-container>
    </section>
  </div>
</template>

<script setup lang="ts">
  import { useTheme } from 'vuetify'

  const store = useSiteContentStore()
  const { about } = storeToRefs(store)
  const productsStore = useProductsStore()

  const theme = useTheme()
  const isDark = computed(() => theme.global.name.value === 'dark')

  // Scoped to this page only — the site-wide theme (#3B5BDB primary) is untouched
  // everywhere else. Light mode swaps in the brand indigo/soft-background look
  // requested for About; dark mode keeps the site's own dark tokens as-is.
  const pageVars = computed(() =>
    isDark.value
      ? {}
      : {
          '--v-theme-primary': '99, 102, 241', // #6366F1
          background: '#F8FAFC',
          color: '#0F172A'
        }
  )

  const storyParagraphs = computed(() => (about.value.story_content || '').split('\n\n').filter(Boolean))

  // Awaited (not onMounted) — this is the whole page's primary content, so
  // it must be present in the server-rendered HTML, not just after
  // hydration. store.about has fallback seed data already, but products
  // does not (would show an empty "What I Build" grid without this).
  await useAsyncData('about-content', async () => {
    await store.fetchAbout()
    return true
  })
  await useAsyncData('about-products', async () => {
    await productsStore.fetchProducts()
    return true
  })
</script>

<style scoped>
  /* ── Hero ──────────────────────────────────────────────────────────────── */
  .hero-section {
    position: relative;
    overflow: hidden;
  }
  .hero-decor {
    position: absolute;
    top: -40px;
    right: -20px;
    width: 300px;
    height: 300px;
    opacity: 0.55;
    pointer-events: none;
  }
  .hero-content {
    position: relative;
    z-index: 1;
    max-width: 720px;
    margin: 0 auto;
    text-align: center;
  }
  .hero-description {
    max-width: 560px;
  }
  .hero-links {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 22px;
  }

  /* ── My Story ──────────────────────────────────────────────────────────── */
  .story-block {
    max-width: 680px;
    margin: 0 auto;
    text-align: center;
  }
  .story-para {
    max-width: 100%;
    margin: 0 auto 12px;
  }
  .story-para:last-child {
    margin-bottom: 0;
  }

  /* ── What I Build ──────────────────────────────────────────────────────── */
  .products-sub {
    max-width: 480px;
    margin: 0 auto;
  }
  .products-grid {
    display: grid;
    gap: 28px;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    max-width: 780px;
    margin: 0 auto;
  }

  /* ── My Approach (numbered cards, unchanged from the previous design) ───── */
  .values-grid {
    display: flex;
    align-items: stretch;
    gap: 16px;
  }
  .value-connector {
    flex: 0 0 32px;
    align-self: center;
    height: 2px;
    margin-top: -40px;
    background: linear-gradient(
      90deg,
      rgba(var(--v-theme-primary), 0.35),
      rgba(var(--v-theme-primary), 0.08)
    );
  }
  .value-card {
    position: relative;
    flex: 1 1 220px;
    padding: 28px 26px;
    border-radius: 20px;
    background: rgb(var(--v-theme-surface));
    border: 1px solid rgba(var(--v-theme-on-surface), 0.06);
    box-shadow: 0 12px 28px rgba(var(--v-theme-on-surface), 0.06);
    text-align: center;
    overflow: hidden;
    transition:
      transform 0.25s ease,
      box-shadow 0.25s ease;
  }
  .value-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 18px 36px rgba(var(--v-theme-on-surface), 0.1);
  }
  .value-num {
    position: absolute;
    top: 8px;
    right: 16px;
    font-size: 2.2rem;
    font-weight: 900;
    line-height: 1;
    color: rgba(var(--v-theme-primary), 0.1);
  }
  @media (max-width: 768px) {
    .values-grid {
      flex-direction: column;
    }
    .value-connector {
      display: none;
    }
  }
  .value-card h3 {
    font-size: 1rem;
    font-weight: 800;
    margin: 14px 0 6px;
  }
  .value-card p {
    font-size: 0.86rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    line-height: 1.55;
    margin: 0;
  }

  /* ── Who I Build For ───────────────────────────────────────────────────── */
  .audience-sub {
    max-width: 460px;
    margin: 0 auto;
  }
  .audience-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 20px;
    max-width: 800px;
    margin: 8px auto 0;
  }
  .audience-card {
    padding: 14px;
    text-align: left;
  }
  .audience-image {
    aspect-ratio: 4 / 3;
    border-radius: 14px;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(145deg, rgba(99, 102, 241, 0.16), rgba(139, 92, 246, 0.14));
    color: rgb(var(--v-theme-primary));
    margin-bottom: 14px;
  }
  .audience-photo {
    width: 100%;
    height: 100%;
  }
  .audience-label {
    display: block;
    font-weight: 700;
    font-size: 0.88rem;
    padding: 0 4px 4px;
  }

  /* ── Personal Profile ──────────────────────────────────────────────────── */
  .profile-avatar {
    box-shadow: 0 16px 40px rgba(var(--v-theme-primary), 0.25);
  }
  .about-lead {
    max-width: 560px;
  }
  .profile-greeting {
    font-size: 0.95rem;
    font-weight: 800;
    color: rgb(var(--v-theme-primary));
    margin-bottom: 4px;
  }
  .profile-name {
    margin-bottom: 10px;
  }
  .skills-row {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 18px;
  }
  .social-row {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
    margin-top: 20px;
  }
  .social-btn {
    width: 40px;
    height: 40px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: rgba(var(--v-theme-on-surface), 0.06);
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
    transition:
      background 0.18s,
      color 0.18s,
      transform 0.15s;
  }
  .social-btn:hover {
    background: rgba(var(--v-theme-primary), 0.12);
    color: rgb(var(--v-theme-primary));
    transform: translateY(-2px);
  }
  @media (min-width: 960px) {
    .social-row {
      justify-content: flex-start;
    }
  }

  /* ── CTA ───────────────────────────────────────────────────────────────── */
  .cta-links {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 22px;
  }
</style>
