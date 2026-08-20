// app/composables/useNavSections.ts
import type { NavSection } from '~/types'

// Drives the "Platform ▼ / Solutions ▼ / Pricing / Resources ▼ / About" nav
// structure from real CMS data (products, solutions) instead of a hardcoded
// list — adding a new product or solution just means a new row in the CMS,
// no nav code changes. Shared by the desktop mega-menu and the mobile drawer
// so both stay in sync automatically.
export function useNavSections() {
  const { t } = useI18n()
  const productsStore = useProductsStore()
  const solutionsStore = useSolutionsStore()

  onMounted(() => {
    productsStore.fetchProducts()
    solutionsStore.fetchSolutions()
  })

  const sections = computed<NavSection[]>(() => [
    {
      key: 'platform',
      type: 'dropdown',
      label: t('menu.platform'),
      items: productsStore.products.map((p) => ({
        label: p.name,
        description: p.tagline,
        to: `/products/${p.slug}`
      })),
      viewAllLabel: t('button.view_all_products'),
      viewAllTo: '/products'
    },
    {
      key: 'solutions',
      type: 'dropdown',
      label: t('menu.solutions'),
      items: solutionsStore.solutions.map((s) => ({
        label: s.name,
        description: s.tagline,
        icon: s.icon,
        to: `/solutions/${s.slug}`
      })),
      viewAllLabel: t('menu.view_all_solutions'),
      viewAllTo: '/solutions'
    },
    {
      key: 'pricing',
      type: 'link',
      label: t('menu.pricing'),
      to: '/pricing'
    },
    {
      key: 'resources',
      type: 'dropdown',
      label: t('menu.resources'),
      items: [
        { label: t('resources.documentation'), icon: 'mdi-book-open-outline', to: '/documentation' },
        { label: t('resources.help_center'), icon: 'mdi-lifebuoy', to: '/help' },
        { label: t('resources.blog'), icon: 'mdi-newspaper-variant-outline', to: '/blog' }
      ]
    },
    {
      key: 'about',
      type: 'link',
      label: t('menu.about'),
      to: '/about'
    }
  ])

  return { sections }
}
