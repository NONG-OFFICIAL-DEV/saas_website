import { defineStore } from 'pinia'
import { ref } from 'vue'
import { getHero, getAbout, getFooter } from '~/services/cms/siteContent'
import type { HeroContent, AboutContent, FooterContent } from '~/types'

// Mirrors the CMS backend's seeded content — used if the API is
// unreachable so the site never breaks or shows blank sections.
const FALLBACK_HERO: HeroContent = {
  badge_text: '🇰🇭 Independent SaaS builder',
  headline: 'One Builder.',
  subheadline: 'Multiple Products, Real Support.',
  description:
    'I design, build, and personally support small SaaS products for real businesses — starting with point-of-sale, and growing from there.',
  trust_line: 'No agencies · No account managers · Direct support from the person who builds it',
  cta_primary_label: 'View Products',
  cta_secondary_label: 'Talk to Me',
  cta_secondary_url: 'https://t.me/Nong_Phloeut',
  stats: [
    { num: '2+', label: 'Products Live' },
    { num: '300+', label: 'Businesses Served' },
    { num: '24/7', label: 'Direct Support' },
    { num: '🇰🇭', label: 'Built in Cambodia' }
  ]
}

const FALLBACK_ABOUT: AboutContent = {
  hero_tag: 'About',
  hero_heading: 'Building simple software for real businesses.',
  hero_description:
    "I'm an independent software developer focused on creating practical tools that help small businesses manage their daily operations more easily.",
  hero_cta_primary_label: 'Explore My Products',
  hero_cta_secondary_label: 'Contact Me',

  story_title: 'Why I build these products',
  story_content:
    'Many small businesses still rely on notebooks, spreadsheets, chat messages, or several disconnected tools to manage their daily operations.\n\nI wanted to build something simpler — software that business owners can understand, use, and grow with without needing technical knowledge.',

  approach_tag: 'My Approach',
  approach_title: 'How I build',
  approach_cards: [
    {
      icon: 'mdi-lightbulb-outline',
      title: 'Simple',
      description: "Software should be easy to understand, even if you're not technical."
    },
    {
      icon: 'mdi-target',
      title: 'Practical',
      description: 'I focus on solving real business problems rather than adding unnecessary complexity.'
    },
    {
      icon: 'mdi-currency-usd',
      title: 'Affordable',
      description: 'I want useful business software to be accessible to small and growing businesses.'
    },
    {
      icon: 'mdi-refresh',
      title: 'Always Improving',
      description: 'Products evolve through real feedback, testing, and continuous improvement.'
    }
  ],

  audience_title: 'Built for small businesses',
  audience_description: "Technology shouldn't be the difficult part of running a business.",
  audience_examples: [
    { icon: 'mdi-camera-outline', label: 'Photography Studios', image_url: '' },
    { icon: 'mdi-coffee-outline', label: 'Coffee Shops', image_url: '' },
    { icon: 'mdi-silverware-fork-knife', label: 'Restaurants', image_url: '' },
    { icon: 'mdi-storefront-outline', label: 'Retail Stores', image_url: '' },
    { icon: 'mdi-store-outline', label: 'Small Businesses', image_url: '' }
  ],

  products_tag: 'What I Build',
  products_title: 'What I build',
  products_description: 'I create practical software designed around real business workflows.',

  email: 'phloeutnong@gmail.com',
  profile_photo_url: '',
  profile_greeting: "Hi, I'm Nong.",
  profile_name: 'Nong Phloeut',
  profile_bio:
    "I'm a software developer who enjoys turning real-world business problems into simple software products.",
  profile_skills: [],
  socials: [
    { name: 'Email', href: 'mailto:phloeutnong@gmail.com' },
    { name: 'Telegram', href: 'https://t.me/Nong_Phloeut' },
    { name: 'Facebook', href: 'https://www.facebook.com/share/18nWjqydNc/?mibextid=wwXIfr' },
    { name: 'TikTok', href: 'https://www.tiktok.com/@nexstack.tech?_r=1' }
  ],

  cta_title: 'Have a business problem I can help solve?',
  cta_description:
    'Whether you need a business management system or have an idea for custom software, feel free to get in touch.',
  cta_primary_label: 'Contact Me',
  cta_secondary_label: 'Explore Products'
}

const FALLBACK_FOOTER: FooterContent = {
  email: 'phloeutnong@gmail.com',
  phone: '066 53 86 01',
  address: 'Phnom Penh, Cambodia',
  socials: [
    { name: 'Telegram', href: 'https://t.me/Nong_Phloeut' },
    { name: 'Facebook', href: 'https://www.facebook.com/share/18nWjqydNc/?mibextid=wwXIfr' },
    { name: 'TikTok', href: 'https://www.tiktok.com/@nexstack.tech?_r=1' }
  ]
}

export const useSiteContentStore = defineStore('siteContent', () => {
  // Start from the fallback so sections render real copy immediately —
  // never a blank flash while the CMS fetch resolves.
  const hero = ref<HeroContent>({ ...FALLBACK_HERO })
  const about = ref<AboutContent>({ ...FALLBACK_ABOUT })
  const footer = ref<FooterContent>({ ...FALLBACK_FOOTER })
  const loaded = ref({ hero: false, about: false, footer: false })

  async function fetchHero() {
    if (loaded.value.hero) return
    loaded.value.hero = true
    try {
      const data = await getHero()
      if (data) hero.value = data
    } catch {
      // keep fallback
    }
  }

  async function fetchAbout() {
    if (loaded.value.about) return
    loaded.value.about = true
    try {
      const data = await getAbout()
      // Merge over the fallback rather than replace it outright: if the CMS
      // row hasn't been re-saved yet through the new About admin form, it
      // may still be missing newer fields — this keeps the page from
      // rendering with undefined arrays instead of falling back cleanly.
      if (data) about.value = { ...FALLBACK_ABOUT, ...data }
    } catch {
      // keep fallback
    }
  }

  async function fetchFooter() {
    if (loaded.value.footer) return
    loaded.value.footer = true
    try {
      const data = await getFooter()
      if (data) footer.value = data
    } catch {
      // keep fallback
    }
  }

  return { hero, about, footer, fetchHero, fetchAbout, fetchFooter }
})
