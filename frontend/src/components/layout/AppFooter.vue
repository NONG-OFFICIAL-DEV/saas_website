<template>
  <footer class="site-footer">
    <!-- Top gradient divider -->
    <div class="footer-glow" aria-hidden="true" />

    <div class="footer-inner">
      <!-- ── Col 1 : Brand + contact ─────────────────────────────── -->
      <div class="footer-col col-brand">
        <!-- Logo -->
        <a href="/" class="footer-logo">
          <img :src="isDark ? '/logo_white.png' : '/logo.png'" alt="Nexstack" class="footer-logo-img" />
        </a>

        <ul class="contact-list">
          <li class="contact-item">
            <span class="contact-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <rect x="2" y="4" width="20" height="16" rx="2" />
                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
              </svg>
            </span>
            <a :href="`mailto:${footer.email}`" class="contact-link">
              {{ footer.email }}
            </a>
          </li>
          <li class="contact-item">
            <span class="contact-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path
                  d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.14 13.5a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.05 2.77h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21 16.92z"
                />
              </svg>
            </span>
            <a :href="`tel:${footer.phone}`" class="contact-link">{{ footer.phone }}</a>
          </li>
          <li class="contact-item">
            <span class="contact-icon">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                width="15"
                height="15"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
              >
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z" />
                <circle cx="12" cy="10" r="3" />
              </svg>
            </span>
            <span class="contact-text">{{ footer.address }}</span>
          </li>
        </ul>
      </div>

      <!-- ── Col 2 : Products ─────────────────────────────────────── -->
      <div class="footer-col">
        <h4 class="col-heading">{{ t('footer.products_heading') }}</h4>
        <ul class="footer-links">
          <li v-for="product in productsStore.products" :key="product.id">
            <router-link :to="`/products/${product.slug}`" class="footer-nav-link">
              <span class="link-dot" />
              {{ product.name }}
            </router-link>
          </li>
          <li>
            <router-link to="/products" class="footer-nav-link">
              <span class="link-dot" />
              {{ t('button.view_all_products') }}
            </router-link>
          </li>
        </ul>
      </div>

      <!-- ── Col 3 : Quick links ──────────────────────────────────── -->
      <div class="footer-col">
        <h4 class="col-heading">{{ t('footer.links_heading') }}</h4>
        <ul class="footer-links">
          <li v-for="link in navLinks" :key="link.to || link.href">
            <component
              :is="link.to ? 'router-link' : 'a'"
              :to="link.to"
              :href="link.href"
              class="footer-nav-link"
            >
              <span class="link-dot" />
              {{ t(link.key) }}
            </component>
          </li>
        </ul>
      </div>

      <!-- ── Col 4 : Platform + social ───────────────────────────── -->
      <div class="footer-col col-platform">
        <h4 class="col-heading">{{ t('footer.platform_heading') }}</h4>

        <!-- Web-only badge -->
        <div class="web-badge">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            width="16"
            height="16"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
            stroke-linecap="round"
            stroke-linejoin="round"
          >
            <circle cx="12" cy="12" r="10" />
            <path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20" />
            <path d="M2 12h20" />
          </svg>
          <div>
            <span class="web-badge-title">{{ t('footer.web_only') }}</span>
            <span class="web-badge-sub">{{ t('footer.web_only_sub') }}</span>
          </div>
        </div>

        <!-- Social links -->
        <h4 class="col-heading mt-social">{{ t('footer.follow_heading') }}</h4>
        <div class="social-links">
          <a
            v-for="social in footer.socials ?? []"
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
      </div>
    </div>

    <!-- ── Bottom bar ────────────────────────────────────────────── -->
    <div class="footer-bottom">
      <div class="footer-bottom-inner">
        <span class="copy-text">{{ t('footer.copy') }}</span>
        <div class="bottom-links">
          <a href="/privacy" target="_blank"  class="bottom-link">{{ t('common.privacy_policy') }}</a>
          <span class="bottom-sep">·</span>
          <a href="/terms" target="_blank" class="bottom-link">{{ t('common.terms_of_service') }}</a>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
  import { computed, onMounted } from 'vue'
  import { storeToRefs } from 'pinia'
  import { useI18n } from 'vue-i18n'
  import { useTheme } from 'vuetify'
  import { useProductsStore } from '@/stores/products'
  import { useSiteContentStore } from '@/stores/siteContent'
  import SocialIcon from '@/components/icons/SocialIcon.vue'

  const { t } = useI18n()
  const theme = useTheme()
  const productsStore = useProductsStore()
  const siteContentStore = useSiteContentStore()
  const { footer } = storeToRefs(siteContentStore)

  onMounted(() => {
    productsStore.fetchProducts()
    siteContentStore.fetchFooter()
  })

  const isDark = computed(() => theme.global.name.value === 'dark')

  // ── Nav links ─────────────────────────────────────────────────────────
  const navLinks = [
    { to: '/', key: 'menu.home' },
    { to: '/products', key: 'menu.products' },
    { to: '/solutions', key: 'menu.solutions' },
    { to: '/pricing', key: 'menu.pricing' },
    { to: '/about', key: 'menu.about' },
    { href: '/#contact', key: 'menu.contact' }
  ]
</script>

<style scoped>
  /* ── Footer wrapper ─────────────────────────────────────────────────── */
  .site-footer {
    position: relative;
    background: rgba(var(--v-theme-surface), 0.95);
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    overflow: hidden;
  }

  /* Soft primary glow at the top edge */
  .footer-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(
      90deg,
      transparent,
      rgba(var(--v-theme-primary), 0.6) 30%,
      rgba(245, 158, 11, 0.5) 70%,
      transparent
    );
  }

  /* ── Main grid ──────────────────────────────────────────────────────── */
  .footer-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 64px 32px 48px;
    display: grid;
    grid-template-columns: 1.6fr 1fr 1fr 1.3fr;
    gap: 40px 32px;
  }
  @media (max-width: 960px) {
    .footer-inner {
      grid-template-columns: 1fr 1fr;
    }
  }
  @media (max-width: 600px) {
    .footer-inner {
      grid-template-columns: 1fr;
      padding: 48px 24px 32px;
    }
  }

  /* ── Brand col ──────────────────────────────────────────────────────── */
  .footer-logo {
    display: inline-block;
    margin-bottom: 14px;
  }
  .footer-logo-img {
    height: 38px;
    width: auto;
    object-fit: contain;
  }

  .brand-tagline {
    font-size: 0.83rem;
    color: rgba(var(--v-theme-on-surface), 0.5);
    line-height: 1.6;
    margin: 0 0 22px;
    max-width: 240px;
  }

  /* Contact list */
  .contact-list {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 12px;
  }
  .contact-item {
    display: flex;
    align-items: flex-start;
    align-items: center;
    gap: 10px;
  }
  .contact-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary));
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }
  .contact-link,
  .contact-text {
    font-size: 0.825rem;
    color: rgba(var(--v-theme-on-surface), 0.65);
    text-decoration: none;
    line-height: 1.55;
    transition: color 0.15s;
  }
  .contact-link:hover {
    color: rgb(var(--v-theme-primary));
  }

  /* ── Column heading ─────────────────────────────────────────────────── */
  .col-heading {
    font-size: 0.72rem;
    font-weight: 800;
    text-transform: uppercase;
    color: rgb(var(--v-theme-primary));
    margin: 0 0 18px;
  }

  /* ── Nav / product links ────────────────────────────────────────────── */
  .footer-links {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }
  .footer-nav-link {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.845rem;
    font-weight: 500;
    color: rgba(var(--v-theme-on-surface), 0.55);
    text-decoration: none;
    transition:
      color 0.15s,
      gap 0.15s;
  }
  .footer-nav-link:hover {
    color: rgb(var(--v-theme-on-surface));
    gap: 12px;
  }
  .link-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: rgba(var(--v-theme-primary), 0.45);
    flex-shrink: 0;
    transition: background 0.15s;
  }
  .footer-nav-link:hover .link-dot {
    background: rgb(var(--v-theme-primary));
  }

  /* ── Platform col ───────────────────────────────────────────────────── */

  /* Web-only badge — replaces app store buttons */
  .web-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-radius: 16px;
    border: 1px solid rgba(var(--v-theme-primary), 0.18);
    background: rgba(var(--v-theme-primary), 0.06);
    box-shadow: 0 8px 20px rgba(var(--v-theme-on-surface), 0.04);
    color: rgb(var(--v-theme-primary));
    margin-bottom: 28px;
  }
  .web-badge svg {
    flex-shrink: 0;
  }
  .web-badge-title {
    display: block;
    font-size: 0.82rem;
    font-weight: 800;
    color: rgb(var(--v-theme-on-surface));
    line-height: 1.2;
  }
  .web-badge-sub {
    display: block;
    font-size: 0.72rem;
    color: rgba(var(--v-theme-on-surface), 0.45);
    margin-top: 2px;
  }

  .mt-social {
    margin-top: 0;
  }

  /* Social icons row */
  .social-links {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }
  .social-btn {
    width: 38px;
    height: 38px;
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

  /* ── Bottom bar ─────────────────────────────────────────────────────── */
  .footer-bottom {
    border-top: 1px solid rgba(var(--v-theme-on-surface), 0.06);
  }
  .footer-bottom-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 18px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
  }
  .copy-text {
    font-size: 0.775rem;
    color: rgba(var(--v-theme-on-surface), 0.35);
  }
  .bottom-links {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .bottom-link {
    font-size: 0.775rem;
    color: rgba(var(--v-theme-on-surface), 0.35);
    text-decoration: none;
    transition: color 0.15s;
  }
  .bottom-link:hover {
    color: rgb(var(--v-theme-primary));
  }
  .bottom-sep {
    color: rgba(var(--v-theme-on-surface), 0.2);
    font-size: 0.775rem;
  }
</style>
