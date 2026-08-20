import { createRouter, createWebHistory } from 'vue-router'
import { useAdminAuthStore } from '@/stores/adminAuth'
import { useLoadingStore } from '@/stores/loadingStore'

const routes = [
  {
    path: '/',
    component: () => import('@/views/LandingLayout.vue'),
    children: [
      {
        path: '',
        name: 'landing',
        component: () => import('@/views/LandingPage.vue')
      },
      {
        path: 'products',
        name: 'products',
        component: () => import('@/views/products/ProductsHub.vue')
      },
      {
        path: 'products/:slug',
        name: 'product-detail',
        component: () => import('@/views/products/ProductDetail.vue')
      },
      {
        path: 'solutions',
        name: 'solutions',
        component: () => import('@/views/solutions/SolutionsHub.vue')
      },
      {
        path: 'solutions/:slug',
        name: 'solution-detail',
        component: () => import('@/views/solutions/SolutionDetail.vue')
      },
      {
        path: 'pricing',
        name: 'pricing',
        component: () => import('@/views/PricingPage.vue')
      },
      {
        path: 'about',
        name: 'about',
        component: () => import('@/views/AboutPage.vue')
      },
      {
        path: 'get-started',
        name: 'get-started',
        component: () => import('@/views/GetStartedPage.vue')
      },
      {
        path: 'onboarding/:slug',
        name: 'onboarding',
        component: () => import('@/views/OnboardingWizard.vue')
      },
      {
        path: 'login',
        name: 'login',
        component: () => import('@/views/LoginPage.vue')
      },
      {
        path: 'docs',
        redirect: '/documentation'
      },
      {
        path: 'documentation',
        name: 'documentation',
        component: () => import('@/views/documentation/DocumentationHome.vue')
      },
      {
        path: 'documentation/:slug',
        name: 'documentation-article',
        component: () => import('@/views/documentation/DocumentationArticle.vue')
      },
      {
        path: 'help',
        name: 'help',
        component: () => import('@/views/HelpCenterPage.vue')
      },
      {
        path: 'blog',
        name: 'blog',
        component: () => import('@/views/blog/BlogHub.vue')
      },
      {
        path: 'blog/:slug',
        name: 'blog-post-detail',
        component: () => import('@/views/blog/BlogPostDetail.vue')
      }
    ]
  },
  { path: '/terms', component: () => import('@/views/legal/TermsPage.vue') },
  {
    path: '/privacy',
    component: () => import('@/views/legal/PrivacyPage.vue')
  },
  {
    path: '/admin/login',
    name: 'admin-login',
    component: () => import('@/views/admin/AdminLogin.vue')
  },
  {
    path: '/admin',
    component: () => import('@/views/admin/AdminLayout.vue'),
    children: [
      {
        path: '',
        name: 'admin-dashboard',
        component: () => import('@/views/admin/AdminDashboard.vue')
      },
      {
        path: 'products/new',
        name: 'admin-product-new',
        component: () => import('@/views/admin/AdminProductEditor.vue')
      },
      {
        path: 'products/:id/edit',
        name: 'admin-product-edit',
        component: () => import('@/views/admin/AdminProductEditor.vue')
      },
      {
        path: 'solutions',
        name: 'admin-solutions',
        component: () => import('@/views/admin/AdminSolutionsDashboard.vue')
      },
      {
        path: 'solutions/new',
        name: 'admin-solution-new',
        component: () => import('@/views/admin/AdminSolutionEditor.vue')
      },
      {
        path: 'solutions/:id/edit',
        name: 'admin-solution-edit',
        component: () => import('@/views/admin/AdminSolutionEditor.vue')
      },
      {
        path: 'testimonials',
        name: 'admin-testimonials',
        component: () => import('@/views/admin/AdminTestimonialsDashboard.vue')
      },
      {
        path: 'testimonials/new',
        name: 'admin-testimonial-new',
        component: () => import('@/views/admin/AdminTestimonialEditor.vue')
      },
      {
        path: 'testimonials/:id/edit',
        name: 'admin-testimonial-edit',
        component: () => import('@/views/admin/AdminTestimonialEditor.vue')
      },
      {
        path: 'blog',
        name: 'admin-blog',
        component: () => import('@/views/admin/AdminBlogDashboard.vue')
      },
      {
        path: 'blog/new',
        name: 'admin-blog-new',
        component: () => import('@/views/admin/AdminBlogEditor.vue')
      },
      {
        path: 'blog/:id/edit',
        name: 'admin-blog-edit',
        component: () => import('@/views/admin/AdminBlogEditor.vue')
      },
      {
        path: 'site-content',
        name: 'admin-site-content',
        component: () => import('@/views/admin/AdminSiteContent.vue')
      },
      {
        path: 'onboarding',
        name: 'admin-onboarding',
        component: () => import('@/views/admin/AdminOnboardingDashboard.vue')
      },
      {
        path: 'documentation/categories',
        name: 'admin-doc-categories',
        component: () => import('@/views/admin/AdminDocCategoriesDashboard.vue')
      },
      {
        path: 'documentation/categories/new',
        name: 'admin-doc-category-new',
        component: () => import('@/views/admin/AdminDocCategoryEditor.vue')
      },
      {
        path: 'documentation/categories/:id/edit',
        name: 'admin-doc-category-edit',
        component: () => import('@/views/admin/AdminDocCategoryEditor.vue')
      },
      {
        path: 'documentation/articles',
        name: 'admin-doc-articles',
        component: () => import('@/views/admin/AdminDocArticlesDashboard.vue')
      },
      {
        path: 'documentation/articles/new',
        name: 'admin-doc-article-new',
        component: () => import('@/views/admin/AdminDocArticleEditor.vue')
      },
      {
        path: 'documentation/articles/:id/edit',
        name: 'admin-doc-article-edit',
        component: () => import('@/views/admin/AdminDocArticleEditor.vue')
      }
    ]
  }
]

let adminAuthReady = null

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      // Browser back/forward — restore where the user was.
      return savedPosition
    }
    if (to.hash) {
      // In-page anchor links, e.g. /#contact.
      return { el: to.hash, behavior: 'smooth' }
    }
    // Any other navigation (nav links, product cards, etc.) — start at the top.
    return { top: 0 }
  }
})

// Thin top progress bar for route transitions — a lazy-loaded route's JS
// chunk (and, for /admin, the auth check below) is a real network fetch
// that can take a moment on a slow connection. This is the one legitimate
// use of the 'bar' loader mode (see stores/loadingStore.js) — it never
// blocks anything, just gives a subtle "something is happening" signal.
//
// Guarded by navigationInFlight rather than starting unconditionally: a
// redirect (e.g. the admin-auth guard below bouncing to /admin/login)
// makes Vue Router re-run the whole beforeEach chain for the new target,
// which would call start() twice for what's really one navigation from
// the user's perspective — but afterEach only fires once, on the
// navigation that actually completes. Without this flag the bar would
// permanently get stuck showing after any redirect.
let navigationInFlight = false

router.beforeEach(() => {
  if (!navigationInFlight) {
    navigationInFlight = true
    useLoadingStore().start('bar')
  }
  return true
})

router.beforeEach(async to => {
  if (!to.path.startsWith('/admin')) return true

  const authStore = useAdminAuthStore()
  adminAuthReady ??= authStore.init()
  await adminAuthReady

  const isLoggedIn = !!authStore.session
  if (to.name === 'admin-login') {
    return isLoggedIn ? { name: 'admin-dashboard' } : true
  }
  return isLoggedIn ? true : { name: 'admin-login' }
})

function stopNavigationBar() {
  if (!navigationInFlight) return
  navigationInFlight = false
  useLoadingStore().stop()
}

router.afterEach(stopNavigationBar)
router.onError(stopNavigationBar)

export default router
