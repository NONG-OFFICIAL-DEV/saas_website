<template>
  <v-app :theme="theme">
    <!-- ── Mobile drawer ─────────────────────────────────────────────── -->
    <v-navigation-drawer
      v-model="drawer"
      temporary
      location="right"
      width="260"
    >
      <v-list class="pa-3">
        <v-list-item
          v-for="link in navLinks"
          :key="link.href"
          :href="link.href"
          rounded="lg"
          class="mb-1"
          @click="drawer = false"
        >
          <v-list-item-title class="font-weight-bold">
            {{ link.label }}
          </v-list-item-title>
        </v-list-item>
        <v-divider class="my-3" />
        <v-list-item>
          <v-btn
            block
            color="amber-darken-2"
            rounded="lg"
            variant="flat"
            href="#contact"
            @click="drawer = false"
          >
            Get Started
          </v-btn>
        </v-list-item>
      </v-list>
    </v-navigation-drawer>

    <!-- ── Navbar ────────────────────────────────────────────────────── -->
    <v-app-bar
      flat
      height="64"
      class="glass-nav px-md-8"
      :elevation="scrolled ? 3 : 0"
    >
      <div class="logo-wrap">
        <span class="logo-icon">🍽️</span>
        <span class="logo-text">
          e
          <span class="accent">Menu</span>
        </span>
      </div>
      <v-spacer />

      <div class="d-none d-md-flex align-center gap-7 mr-8">
        <a
          v-for="link in navLinks"
          :key="link.href"
          :href="link.href"
          class="nav-link"
        >
          {{ link.label }}
        </a>
      </div>

      <div class="d-flex align-center gap-2">
        <!-- Lang -->
        <v-btn variant="tonal" rounded="lg" size="small" min-width="56">
          {{ locale === 'en' ? 'EN' : 'KM' }}
          <v-icon end size="13">mdi-chevron-down</v-icon>
          <v-menu activator="parent">
            <v-list
              density="compact"
              rounded="lg"
              elevation="4"
              min-width="120"
            >
              <v-list-item rounded="lg" @click="locale = 'en'">
                <v-list-item-title class="text-body-2">
                  🇬🇧 English
                </v-list-item-title>
              </v-list-item>
              <v-list-item rounded="lg" @click="locale = 'km'">
                <v-list-item-title class="text-body-2">
                  🇰🇭 ខ្មែរ
                </v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </v-btn>

        <v-btn
          :icon="theme === 'light' ? 'mdi-weather-night' : 'mdi-weather-sunny'"
          variant="text"
          size="small"
          @click="theme = theme === 'light' ? 'dark' : 'light'"
        />

        <v-btn
          color="amber-darken-2"
          variant="flat"
          rounded="lg"
          size="small"
          class="d-none d-md-flex px-5"
          href="#contact"
        >
          Get Started
        </v-btn>

        <v-app-bar-nav-icon class="d-md-none" @click="drawer = !drawer" />
      </div>
    </v-app-bar>

    <v-main>
      <v-container fluid class="pa-0">
        <!-- ── HERO ──────────────────────────────────────────────────────── -->
        <section class="hero-section">
          <div class="hero-orb hero-orb-1" />
          <div class="hero-orb hero-orb-2" />
          <div class="hero-orb hero-orb-3" />

          <v-container>
            <v-row align="center" justify="center">
              <!-- Left text -->
              <v-col
                cols="12"
                md="6"
                class="hero-text-col"
                data-aos="fade-right"
              >
                <div class="hero-badge mb-6">
                  <span class="badge-dot" />
                  {{ t('emenu.hero.badge') }}
                </div>

                <h1 class="hero-title mb-5">
                  {{ t('emenu.hero.title1') }}
                  <br />
                  <span class="gold-text">{{ t('emenu.hero.title2') }}</span>
                </h1>

                <p class="hero-sub mb-8">{{ t('emenu.hero.sub') }}</p>

                <div class="d-flex gap-3 flex-wrap">
                  <v-btn
                    color="amber-darken-2"
                    size="x-large"
                    rounded="lg"
                    variant="flat"
                    class="hero-btn px-10"
                    append-icon="mdi-arrow-right"
                    href="#contact"
                  >
                    {{ t('emenu.hero.cta') }}
                  </v-btn>
                  <v-btn
                    size="x-large"
                    rounded="lg"
                    variant="outlined"
                    class="px-8"
                    href="#demo"
                  >
                    {{ t('emenu.hero.demo') }}
                  </v-btn>
                </div>

                <!-- Stats -->
                <div class="hero-stats mt-10">
                  <div v-for="s in heroStats" :key="s.num" class="hero-stat">
                    <div class="hstat-num">{{ s.num }}</div>
                    <div class="hstat-label">{{ t(s.key) }}</div>
                  </div>
                </div>
              </v-col>

              <!-- Right: phone mockup -->
              <v-col
                cols="12"
                md="6"
                class="d-flex justify-center align-center"
                data-aos="fade-left"
                data-aos-delay="150"
              >
                <div class="phone-wrap">
                  <div class="phone-frame">
                    <!-- Phone screen -->
                    <div class="phone-screen">
                      <!-- Header -->
                      <div class="ps-header">
                        <div class="ps-restaurant">
                          <span class="ps-logo">🍜</span>
                          <div>
                            <div class="ps-name">ភោជនីយដ្ឋាន នគរ</div>
                            <div class="ps-table">Table 5</div>
                          </div>
                        </div>
                      </div>
                      <!-- Category pills -->
                      <div class="ps-cats">
                        <div
                          v-for="c in phoneCats"
                          :key="c"
                          class="ps-cat"
                          :class="{ active: c === '🍜 Noodles' }"
                        >
                          {{ c }}
                        </div>
                      </div>
                      <!-- Items -->
                      <div class="ps-items">
                        <div
                          v-for="item in phoneItems"
                          :key="item.name"
                          class="ps-item"
                        >
                          <div
                            class="ps-item-img"
                            :style="`background:${item.bg}`"
                          >
                            {{ item.emoji }}
                          </div>
                          <div class="ps-item-info">
                            <div class="ps-item-name">{{ item.name }}</div>
                            <div class="ps-item-price">${{ item.price }}</div>
                          </div>
                          <div class="ps-add">+</div>
                        </div>
                      </div>
                      <!-- Cart bar -->
                      <div class="ps-cart">
                        <div class="ps-cart-count">3 items</div>
                        <div class="ps-cart-total">View Order · $12.50</div>
                      </div>
                    </div>
                  </div>

                  <!-- Floating elements around phone -->
                  <div
                    class="float-el float-qr"
                    data-aos="zoom-in"
                    data-aos-delay="400"
                  >
                    <div class="qr-grid">
                      <div
                        v-for="n in 16"
                        :key="n"
                        class="qr-cell"
                        :class="{ dark: qrPattern.includes(n) }"
                      />
                    </div>
                    <div class="qr-label">Scan to order</div>
                  </div>

                  <div
                    class="float-el float-order"
                    data-aos="zoom-in"
                    data-aos-delay="550"
                  >
                    <v-icon
                      icon="mdi-check-circle"
                      color="success"
                      size="16"
                      class="mr-1"
                    />
                    <span>Order sent to kitchen!</span>
                  </div>

                  <div
                    class="float-el float-lang"
                    data-aos="zoom-in"
                    data-aos-delay="650"
                  >
                    🇰🇭 ខ្មែរ / 🇬🇧 EN
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </section>

        <!-- ── HOW IT WORKS ────────────────────────────────────────────────── -->
        <section class="section-pad bg-soft" id="demo">
          <v-container>
            <div class="text-center mb-14" data-aos="fade-up">
              <div class="section-tag">{{ t('emenu.how.tag') }}</div>
              <h2 class="section-title">{{ t('emenu.how.title') }}</h2>
              <p class="section-sub mx-auto">{{ t('emenu.how.sub') }}</p>
            </div>

            <v-row justify="center">
              <v-col
                v-for="(step, i) in howSteps"
                :key="step.key"
                cols="12"
                sm="4"
                data-aos="fade-up"
                :data-aos-delay="i * 120"
              >
                <div class="how-card">
                  <div class="how-num">0{{ i + 1 }}</div>
                  <div class="how-icon-wrap">{{ step.emoji }}</div>
                  <h3 class="how-title">{{ t(step.key + '.title') }}</h3>
                  <p class="how-desc">{{ t(step.key + '.desc') }}</p>
                  <!-- Arrow between steps -->
                  <div v-if="i < 2" class="how-arrow">→</div>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </section>

        <!-- ── FEATURES ───────────────────────────────────────────────────── -->
        <section class="section-pad" id="features">
          <v-container>
            <div class="text-center mb-14" data-aos="fade-up">
              <div class="section-tag">{{ t('emenu.features.tag') }}</div>
              <h2 class="section-title">{{ t('emenu.features.title') }}</h2>
            </div>

            <!-- Big feature 1: Menu display -->
            <v-row align="center" class="mb-16">
              <v-col cols="12" md="6" data-aos="fade-right">
                <div class="feat-visual feat-v1">
                  <div class="feat-mockup">
                    <div class="fm-header">📱 Live Menu Preview</div>
                    <div class="fm-grid">
                      <div
                        v-for="m in featMenuItems"
                        :key="m.name"
                        class="fm-card"
                      >
                        <div class="fm-img" :style="`background:${m.bg}`">
                          {{ m.emoji }}
                        </div>
                        <div class="fm-name">{{ m.name }}</div>
                        <div class="fm-price">{{ m.price }}</div>
                      </div>
                    </div>
                  </div>
                  <div class="feat-float feat-float-1">
                    <v-icon
                      icon="mdi-image-multiple"
                      size="13"
                      class="mr-1"
                      color="amber"
                    />
                    HD food photos
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="6" class="pl-md-14" data-aos="fade-left">
                <div class="section-tag">{{ t('emenu.f1.tag') }}</div>
                <h3 class="feat-title">{{ t('emenu.f1.title') }}</h3>
                <p class="feat-desc">{{ t('emenu.f1.desc') }}</p>
                <div class="feat-checks mt-6">
                  <div
                    v-for="k in ['emenu.f1.c1', 'emenu.f1.c2', 'emenu.f1.c3']"
                    :key="k"
                    class="feat-check"
                  >
                    <div class="check-dot" />
                    {{ t(k) }}
                  </div>
                </div>
              </v-col>
            </v-row>

            <!-- Big feature 2: Order directly -->
            <v-row align="center" class="mb-16 flex-md-row-reverse">
              <v-col cols="12" md="6" data-aos="fade-left">
                <div class="feat-visual feat-v2">
                  <div class="order-flow">
                    <div
                      v-for="(step, i) in orderFlow"
                      :key="step.label"
                      class="of-step"
                    >
                      <div class="of-icon">{{ step.icon }}</div>
                      <div class="of-label">{{ step.label }}</div>
                      <div v-if="i < orderFlow.length - 1" class="of-arrow">
                        ↓
                      </div>
                    </div>
                  </div>
                  <div class="feat-float feat-float-2">
                    <v-icon
                      icon="mdi-lightning-bolt"
                      size="13"
                      class="mr-1"
                      color="success"
                    />
                    Instant to kitchen
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="6" class="pr-md-14" data-aos="fade-right">
                <div class="section-tag">{{ t('emenu.f2.tag') }}</div>
                <h3 class="feat-title">{{ t('emenu.f2.title') }}</h3>
                <p class="feat-desc">{{ t('emenu.f2.desc') }}</p>
                <div class="feat-checks mt-6">
                  <div
                    v-for="k in ['emenu.f2.c1', 'emenu.f2.c2', 'emenu.f2.c3']"
                    :key="k"
                    class="feat-check"
                  >
                    <div class="check-dot" />
                    {{ t(k) }}
                  </div>
                </div>
              </v-col>
            </v-row>

            <!-- Big feature 3: Bilingual -->
            <v-row align="center">
              <v-col cols="12" md="6" data-aos="fade-right">
                <div class="feat-visual feat-v3">
                  <div class="lang-demo">
                    <div class="lang-toggle-demo">
                      <button class="ltd-btn active">🇰🇭 ខ្មែរ</button>
                      <button class="ltd-btn">🇬🇧 English</button>
                    </div>
                    <div class="lang-content">
                      <div class="lc-item">
                        <span class="lc-kh">ស្បៃជ្រូក</span>
                        <span class="lc-en">→ Grilled Pork</span>
                      </div>
                      <div class="lc-item">
                        <span class="lc-kh">បាយឆា</span>
                        <span class="lc-en">→ Fried Rice</span>
                      </div>
                      <div class="lc-item">
                        <span class="lc-kh">ក្រូចឆ្មារ</span>
                        <span class="lc-en">→ Fresh Lime Juice</span>
                      </div>
                      <div class="lc-item">
                        <span class="lc-kh">នំបញ្ចុក</span>
                        <span class="lc-en">→ Khmer Noodles</span>
                      </div>
                    </div>
                  </div>
                </div>
              </v-col>
              <v-col cols="12" md="6" class="pl-md-14" data-aos="fade-left">
                <div class="section-tag">{{ t('emenu.f3.tag') }}</div>
                <h3 class="feat-title">{{ t('emenu.f3.title') }}</h3>
                <p class="feat-desc">{{ t('emenu.f3.desc') }}</p>
                <div class="feat-checks mt-6">
                  <div
                    v-for="k in ['emenu.f3.c1', 'emenu.f3.c2', 'emenu.f3.c3']"
                    :key="k"
                    class="feat-check"
                  >
                    <div class="check-dot" />
                    {{ t(k) }}
                  </div>
                </div>
              </v-col>
            </v-row>
          </v-container>
        </section>

        <!-- ── FEATURE CARDS GRID ─────────────────────────────────────────── -->
        <section class="section-pad bg-soft">
          <v-container>
            <div class="text-center mb-12" data-aos="fade-up">
              <div class="section-tag">{{ t('emenu.more.tag') }}</div>
              <h2 class="section-title">{{ t('emenu.more.title') }}</h2>
            </div>
            <v-row dense>
              <v-col
                v-for="(f, i) in featureCards"
                :key="f.titleKey"
                cols="12"
                sm="6"
                md="4"
                data-aos="fade-up"
                :data-aos-delay="i * 60"
              >
                <v-card
                  rounded="lg"
                  border
                  variant="flat"
                  class="fcard pa-7 fill-height"
                  hover
                >
                  <v-avatar
                    :color="f.color"
                    variant="tonal"
                    rounded="lg"
                    size="52"
                    class="mb-5"
                  >
                    <v-icon :icon="f.icon" size="24" />
                  </v-avatar>
                  <div class="text-body-1 font-weight-bold mb-2">
                    {{ t(f.titleKey) }}
                  </div>
                  <div
                    class="text-body-2 text-medium-emphasis"
                    style="line-height: 1.7"
                  >
                    {{ t(f.descKey) }}
                  </div>
                </v-card>
              </v-col>
            </v-row>
          </v-container>
        </section>

        <!-- ── CTA ───────────────────────────────────────────────────────── -->
        <section class="section-pad" id="contact">
          <v-container>
            <div class="cta-block" data-aos="zoom-in">
              <div class="cta-orb cta-orb-1" />
              <div class="cta-orb cta-orb-2" />
              <div class="cta-inner text-center">
                <div class="cta-emoji mb-4">📱</div>
                <h2 class="cta-title text-white mb-3">
                  {{ t('emenu.cta.title') }}
                </h2>
                <p class="cta-sub mb-8">{{ t('emenu.cta.sub') }}</p>
                <div class="d-flex gap-3 justify-center flex-wrap">
                  <v-btn
                    color="amber-darken-1"
                    variant="flat"
                    size="x-large"
                    rounded="lg"
                    class="px-10 font-weight-bold cta-btn"
                    href="mailto:support@Nexstack.app"
                  >
                    {{ t('emenu.cta.btn') }}
                  </v-btn>
                  <v-btn
                    variant="outlined"
                    color="white"
                    size="x-large"
                    rounded="lg"
                    class="px-10"
                    href="/"
                  >
                    {{ t('emenu.cta.back') }}
                  </v-btn>
                </div>
              </div>
            </div>
          </v-container>
        </section>

        <!-- ── FOOTER ────────────────────────────────────────────────────── -->
        <v-footer border="t" class="py-6">
          <v-container>
            <div
              class="d-flex align-center justify-space-between flex-wrap gap-4"
            >
              <div class="logo-wrap">
                <span class="logo-icon">🍽️</span>
                <span class="logo-text">
                  e
                  <span class="accent">Menu</span>
                </span>
                <span class="footer-by">by Nexstack</span>
              </div>
              <div class="text-caption text-medium-emphasis">
                © 2026 Nexstack · Built for Cambodia 🇰🇭
              </div>
            </div>
          </v-container>
        </v-footer>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
  import { ref, onMounted, onUnmounted } from 'vue'
  import { useI18n } from 'vue-i18n'
  import AOS from 'aos'
  import 'aos/dist/aos.css'

  const { t, locale } = useI18n()
  const theme = ref('light')
  const drawer = ref(false)
  const scrolled = ref(false)

  const onScroll = () => {
    scrolled.value = window.scrollY > 30
  }
  onMounted(() => {
    window.addEventListener('scroll', onScroll)
    AOS.init({
      duration: 700,
      easing: 'ease-out-cubic',
      once: true,
      disable: 'mobile'
    })
  })
  onUnmounted(() => window.removeEventListener('scroll', onScroll))

  const navLinks = [
    { href: '#demo', label: 'How it works' },
    { href: '#features', label: 'Features' },
    { href: '#contact', label: 'Contact' }
  ]

  const heroStats = [
    { num: '< 1min', key: 'emenu.stats.setup' },
    { num: '2 langs', key: 'emenu.stats.lang' },
    { num: 'QR', key: 'emenu.stats.qr' },
    { num: 'Live', key: 'emenu.stats.live' }
  ]

  const howSteps = [
    { emoji: '🖨️', key: 'emenu.how.s1' },
    { emoji: '📱', key: 'emenu.how.s2' },
    { emoji: '🍜', key: 'emenu.how.s3' }
  ]

  const phoneCats = ['🍽 All', '🍜 Noodles', '🍚 Rice', '🥤 Drinks']
  const phoneItems = [
    { name: 'Beef Noodle', price: '5.50', emoji: '🍜', bg: '#431407' },
    { name: 'Fried Rice', price: '3.50', emoji: '🍳', bg: '#713f12' },
    { name: 'Iced Coffee', price: '2.00', emoji: '☕', bg: '#1c0a00' }
  ]

  const qrPattern = [1, 2, 4, 5, 6, 7, 9, 12, 16, 14, 13, 11, 8, 3, 10, 15]

  const featMenuItems = [
    { name: 'Beef Noodle', price: '$5.50', emoji: '🍜', bg: '#431407' },
    { name: 'Lok Lak', price: '$7.00', emoji: '🥩', bg: '#7f1d1d' },
    { name: 'Amok Fish', price: '$6.50', emoji: '🐟', bg: '#0c4a6e' },
    { name: 'Iced Coffee', price: '$2.00', emoji: '☕', bg: '#1c0a00' }
  ]

  const orderFlow = [
    { icon: '📱', label: 'Customer scans QR' },
    { icon: '🛒', label: 'Selects items' },
    { icon: '✅', label: 'Confirms order' },
    { icon: '🍳', label: 'Kitchen receives' }
  ]

  const featureCards = [
    {
      icon: 'mdi-qrcode',
      color: 'amber',
      titleKey: 'emenu.fc.qr.title',
      descKey: 'emenu.fc.qr.desc'
    },
    {
      icon: 'mdi-image-multiple-outline',
      color: 'primary',
      titleKey: 'emenu.fc.photo.title',
      descKey: 'emenu.fc.photo.desc'
    },
    {
      icon: 'mdi-translate',
      color: 'teal',
      titleKey: 'emenu.fc.lang.title',
      descKey: 'emenu.fc.lang.desc'
    },
    {
      icon: 'mdi-tune-variant',
      color: 'purple',
      titleKey: 'emenu.fc.mod.title',
      descKey: 'emenu.fc.mod.desc'
    },
    {
      icon: 'mdi-sync',
      color: 'indigo',
      titleKey: 'emenu.fc.live.title',
      descKey: 'emenu.fc.live.desc'
    },
    {
      icon: 'mdi-cellphone',
      color: 'cyan',
      titleKey: 'emenu.fc.mobile.title',
      descKey: 'emenu.fc.mobile.desc'
    }
  ]
</script>

<style scoped>
  @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Noto+Sans+Khmer:wght@400;600;700;900&display=swap');

  * {
    font-family: 'Plus Jakarta Sans', 'Noto Sans Khmer', sans-serif;
    box-sizing: border-box;
  }

  /* --- CRITICAL FIX FOR HORIZONTAL SCROLL --- */

  /* 1. Prevent horizontal scroll on the entire app root */
  :deep(.v-application) {
    overflow-x: hidden !important;
    width: 100vw;
    position: relative;
  }

  /* 2. Clip overflow on the main content wrapper */
  :deep(.v-main) {
    overflow-x: hidden !important;
    display: block; /* Ensures it behaves as a block container */
  }

  /* 3. Ensure sections don't leak width */
  section {
    width: 100%;
    overflow: hidden; /* Localizes clipping to each section */
  }

  /* 4. Fix for AOS animations */
  [data-aos] {
    pointer-events: none; /* Prevents invisible animated boxes from blocking clicks */
  }
  [data-aos].aos-animate {
    pointer-events: auto;
  }

  .logo-wrap {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .logo-icon {
    font-size: 1.4rem;
  }
  .logo-text {
    font-size: 1.35rem;
    font-weight: 900;
    letter-spacing: -0.5px;
    color: rgb(var(--v-theme-on-surface));
  }
  .accent {
    color: #f59e0b;
  }
  .footer-by {
    font-size: 0.72rem;
    color: rgba(var(--v-theme-on-surface), 0.35);
    margin-left: 4px;
    font-weight: 600;
  }
  .nav-link {
    font-size: 0.875rem;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
    transition: color 0.15s;
  }
  .nav-link:hover {
    color: #f59e0b;
  }
  .gap-7 {
    gap: 28px;
  }
  .gap-4 {
    gap: 16px;
  }
  .gap-3 {
    gap: 12px;
  }
  .gap-2 {
    gap: 8px;
  }

  /* ── Hero ── */
  .hero-section {
    padding: 90px 0 80px;
    position: relative;
    overflow: hidden;
    min-height: 90vh;
    display: flex;
    align-items: center;
  }
  .hero-orb {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    pointer-events: none;
  }
  .hero-orb-1 {
    width: 500px;
    height: 500px;
    background: rgba(245, 158, 11, 0.07);
    top: -100px;
    right: -80px;
  }
  .hero-orb-2 {
    width: 400px;
    height: 400px;
    background: rgba(99, 102, 241, 0.05);
    bottom: -80px;
    left: -60px;
  }
  .hero-orb-3 {
    width: 300px;
    height: 300px;
    background: rgba(20, 184, 166, 0.04);
    top: 40%;
    left: 30%;
    transform: translateX(-50%);
  }

  .hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: rgba(245, 158, 11, 0.1);
    border: 1px solid rgba(245, 158, 11, 0.25);
    color: #d97706;
    font-size: 0.78rem;
    font-weight: 700;
    padding: 7px 16px;
    border-radius: 999px;
    letter-spacing: 0.3px;
  }
  .badge-dot {
    width: 7px;
    height: 7px;
    background: #f59e0b;
    border-radius: 50%;
    display: inline-block;
    animation: pulse 2s infinite;
  }
  @keyframes pulse {
    0%,
    100% {
      transform: scale(1);
      opacity: 1;
    }
    50% {
      transform: scale(1.6);
      opacity: 0.5;
    }
  }

  .hero-title {
    font-size: clamp(2.4rem, 5.5vw, 4.4rem);
    font-weight: 900;
    letter-spacing: -2px;
    line-height: 1.1;
  }
  .gold-text {
    background: linear-gradient(120deg, #f59e0b, #fbbf24, #d97706);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
  }
  .hero-sub {
    font-size: 1.1rem;
    opacity: 0.65;
    line-height: 1.75;
    max-width: 500px;
  }
  .hero-btn {
    box-shadow: 0 8px 28px rgba(245, 158, 11, 0.3) !important;
  }
  .hero-stats {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
  }
  .hero-stat {
    text-align: center;
  }
  .hstat-num {
    font-size: 1.4rem;
    font-weight: 900;
    color: #f59e0b;
    letter-spacing: -0.5px;
  }
  .hstat-label {
    font-size: 0.7rem;
    opacity: 0.55;
    font-weight: 600;
    margin-top: 2px;
  }

  /* ── Phone mockup ── */
  .phone-wrap {
    position: relative;
    width: 260px;
  }
  .phone-frame {
    width: 260px;
    height: 520px;
    background: linear-gradient(145deg, #1e293b, #0f172a);
    border-radius: 40px;
    padding: 10px;
    box-shadow:
      0 32px 80px rgba(0, 0, 0, 0.3),
      inset 0 0 0 1px rgba(255, 255, 255, 0.06);
    position: relative;
  }
  .phone-screen {
    width: 100%;
    height: 100%;
    background: #0f172a;
    border-radius: 32px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
  }

  /* Phone content */
  .ps-header {
    background: linear-gradient(160deg, #1a0a00, #2d1500);
    padding: 14px 12px 10px;
  }
  .ps-restaurant {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .ps-logo {
    font-size: 1.4rem;
  }
  .ps-name {
    font-size: 0.68rem;
    font-weight: 800;
    color: #fff;
    font-family: 'Noto Sans Khmer', sans-serif;
  }
  .ps-table {
    font-size: 0.58rem;
    color: rgba(255, 255, 255, 0.45);
  }

  .ps-cats {
    display: flex;
    gap: 5px;
    padding: 8px 10px;
    overflow-x: auto;
    scrollbar-width: none;
    background: #0f172a;
  }
  .ps-cat {
    padding: 4px 10px;
    border-radius: 999px;
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.6rem;
    font-weight: 700;
    white-space: nowrap;
    flex-shrink: 0;
  }
  .ps-cat.active {
    background: rgba(251, 191, 36, 0.15);
    border-color: rgba(251, 191, 36, 0.35);
    color: #fbbf24;
  }

  .ps-items {
    flex: 1;
    padding: 6px 10px;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .ps-item {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #1e293b;
    border-radius: 10px;
    padding: 6px;
  }
  .ps-item-img {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    flex-shrink: 0;
  }
  .ps-item-info {
    flex: 1;
  }
  .ps-item-name {
    font-size: 0.6rem;
    font-weight: 700;
    color: #f1f5f9;
  }
  .ps-item-price {
    font-size: 0.58rem;
    color: #fbbf24;
    font-weight: 700;
  }
  .ps-add {
    width: 22px;
    height: 22px;
    background: #fbbf24;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: 900;
    color: #000;
    flex-shrink: 0;
  }

  .ps-cart {
    background: linear-gradient(90deg, #f59e0b, #fbbf24);
    margin: 8px 10px;
    border-radius: 10px;
    padding: 8px 12px;
    display: flex;
    justify-content: space-between;
    align-items: center;
  }
  .ps-cart-count {
    font-size: 0.6rem;
    font-weight: 800;
    color: #000;
    background: rgba(0, 0, 0, 0.15);
    padding: 2px 8px;
    border-radius: 999px;
  }
  .ps-cart-total {
    font-size: 0.65rem;
    font-weight: 900;
    color: #000;
  }

  /* Floating elements */
  .float-el {
    position: absolute;
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(8px);
    border-radius: 12px;
    padding: 8px 12px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #1e293b;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.12);
    white-space: nowrap;
    z-index: 10;
  }
  .float-qr {
    top: -20px;
    right: -50px;
    flex-direction: column;
    gap: 6px;
    padding: 10px;
  }
  .float-order {
    bottom: 60px;
    right: -70px;
    gap: 4px;
  }
  .float-lang {
    bottom: 10px;
    left: -60px;
    gap: 4px;
  }

  /* QR mock */
  .qr-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 2px;
  }
  .qr-cell {
    width: 10px;
    height: 10px;
    background: #e2e8f0;
    border-radius: 1px;
  }
  .qr-cell.dark {
    background: #1e293b;
  }
  .qr-label {
    font-size: 0.6rem;
    font-weight: 700;
    color: #64748b;
    text-align: center;
  }

  /* ── Sections ── */
  .section-pad {
    padding: 88px 0;
  }
  .bg-soft {
    background: rgba(var(--v-theme-surface-variant), 0.25);
  }
  .section-tag {
    font-size: 0.7rem;
    font-weight: 800;
    letter-spacing: 2px;
    color: #f59e0b;
    text-transform: uppercase;
    margin-bottom: 10px;
  }
  .section-title {
    font-size: clamp(1.7rem, 3.5vw, 2.6rem);
    font-weight: 900;
    letter-spacing: -1px;
    line-height: 1.12;
    margin-bottom: 14px;
  }
  .section-sub {
    font-size: 0.95rem;
    opacity: 0.6;
    line-height: 1.75;
    max-width: 500px;
  }

  /* ── How it works ── */
  .how-card {
    text-align: center;
    padding: 32px 24px;
    position: relative;
  }
  .how-num {
    font-size: 3.5rem;
    letter-spacing: -2px;
    line-height: 1;
    margin-bottom: 4px;
  }
  .how-icon-wrap {
    font-size: 2.8rem;
    margin-bottom: 16px;
  }
  .how-title {
    font-size: 1rem;
    font-weight: 800;
    margin-bottom: 8px;
  }
  .how-desc {
    font-size: 0.875rem;
    opacity: 0.6;
    line-height: 1.6;
  }
  .how-arrow {
    position: absolute;
    top: 50%;
    right: -16px;
    transform: translateY(-50%);
    font-size: 1.5rem;
    display: none;
  }
  @media (min-width: 600px) {
    .how-arrow {
      display: block;
    }
  }

  /* ── Feature visuals ── */
  .feat-visual {
    border-radius: 24px;
    padding: 32px;
    min-height: 320px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    overflow: hidden;
  }
  .feat-v1 {
    background: linear-gradient(135deg, #1a0a00, #2d1500);
  }
  .feat-v2 {
    background: linear-gradient(135deg, #0f172a, #1e1b4b);
  }
  .feat-v3 {
    background: linear-gradient(135deg, #042f2e, #0f172a);
  }

  .feat-title {
    font-size: 1.5rem;
    font-weight: 900;
    letter-spacing: -0.5px;
    margin-bottom: 12px;
  }
  .feat-desc {
    font-size: 0.95rem;
    opacity: 0.65;
    line-height: 1.75;
  }
  .feat-checks {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .feat-check {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.875rem;
    opacity: 0.7;
  }
  .check-dot {
    width: 7px;
    height: 7px;
    background: #f59e0b;
    border-radius: 50%;
    flex-shrink: 0;
  }

  /* Feature mockup */
  .feat-mockup {
    background: #1e293b;
    border-radius: 16px;
    padding: 16px;
    width: 100%;
    max-width: 320px;
  }
  .fm-header {
    font-size: 0.72rem;
    font-weight: 800;
    color: rgba(255, 255, 255, 0.4);
    margin-bottom: 12px;
  }
  .fm-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
  }
  .fm-card {
    background: #0f172a;
    border-radius: 10px;
    overflow: hidden;
  }
  .fm-img {
    height: 64px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
  }
  .fm-name {
    font-size: 0.62rem;
    font-weight: 700;
    color: #f1f5f9;
    padding: 4px 8px 2px;
  }
  .fm-price {
    font-size: 0.6rem;
    color: #fbbf24;
    font-weight: 700;
    padding: 0 8px 6px;
  }

  /* Order flow */
  .order-flow {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
    width: 100%;
    max-width: 220px;
  }
  .of-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0;
  }
  .of-icon {
    width: 60px;
    height: 60px;
    background: #1e293b;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.3);
  }
  .of-label {
    font-size: 0.72rem;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.7);
    margin: 8px 0 0;
    text-align: center;
  }
  .of-arrow {
    font-size: 1.2rem;
    color: rgba(255, 255, 255, 0.2);
    line-height: 1;
    margin: 4px 0;
  }

  /* Lang demo */
  .lang-demo {
    background: #1e293b;
    border-radius: 16px;
    padding: 20px;
    width: 100%;
    max-width: 300px;
  }
  .lang-toggle-demo {
    display: flex;
    gap: 8px;
    margin-bottom: 16px;
  }
  .ltd-btn {
    padding: 7px 16px;
    border-radius: 10px;
    border: 1.5px solid rgba(255, 255, 255, 0.12);
    background: transparent;
    color: rgba(255, 255, 255, 0.4);
    font-size: 0.75rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
  }
  .ltd-btn.active {
    background: rgba(245, 158, 11, 0.15);
    border-color: #f59e0b;
    color: #fbbf24;
  }
  .lang-content {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .lc-item {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .lc-kh {
    font-size: 0.82rem;
    font-weight: 700;
    color: #f1f5f9;
    font-family: 'Noto Sans Khmer', sans-serif;
    min-width: 80px;
  }
  .lc-en {
    font-size: 0.75rem;
    color: rgba(255, 255, 255, 0.4);
  }

  /* Floating feature chips */
  .feat-float {
    position: absolute;
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.95);
    border-radius: 999px;
    padding: 6px 12px;
    font-size: 0.7rem;
    font-weight: 700;
    color: #1e293b;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    white-space: nowrap;
  }
  .feat-float-1 {
    bottom: 16px;
    right: 16px;
  }
  .feat-float-2 {
    top: 16px;
    right: 16px;
  }

  /* ── Feature cards ── */
  .fcard {
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
  }
  .fcard:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.07) !important;
  }

  /* ── CTA ── */
  .cta-block {
    background: linear-gradient(135deg, #92400e 0%, #b45309 40%, #d97706 100%);
    border-radius: 28px;
    padding: 80px 40px;
    position: relative;
    overflow: hidden;
  }
  .cta-orb {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
  }
  .cta-orb-1 {
    width: 400px;
    height: 400px;
    background: rgba(255, 255, 255, 0.06);
    top: -150px;
    right: -100px;
  }
  .cta-orb-2 {
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.04);
    bottom: -100px;
    left: -60px;
  }
  .cta-inner {
    position: relative;
    z-index: 1;
  }
  .cta-emoji {
    font-size: 3.5rem;
  }
  .cta-title {
    font-size: clamp(1.8rem, 3.5vw, 2.8rem);
    font-weight: 900;
    letter-spacing: -1px;
  }
  .cta-sub {
    font-size: 1.05rem;
    color: rgba(255, 255, 255, 0.75);
    max-width: 480px;
    margin: 0 auto;
  }
  .cta-btn {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2) !important;
  }

  @media (max-width: 768px) {
    .hero-section {
      padding: 70px 0 60px;
      min-height: auto;
    }
    .section-pad {
      padding: 64px 0;
    }
    .phone-frame {
      width: 220px;
      height: 440px;
    }
  }
</style>
