import { createRouter, createWebHistory } from 'vue-router'
import { useAdminAuthStore } from '@/stores/adminAuth'

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
        path: 'about',
        name: 'about',
        component: () => import('@/views/AboutPage.vue')
      }
    ]
  },
  {
    path: '/auth/register',
    name: 'register',
    component: () => import('@/views/Register.vue')
  },
  { path: '/terms', component: () => import('@/views/legal/TermsPage.vue') },
  {
    path: '/privacy',
    component: () => import('@/views/legal/PrivacyPage.vue')
  },
  {
    path: '/emenu',
    name: 'EMenuLanding',
    component: () => import('@/views/EMenuLanding.vue')
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
        path: 'site-content',
        name: 'admin-site-content',
        component: () => import('@/views/admin/AdminSiteContent.vue')
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

// router.beforeEach(async to => {
//   if (!to.path.startsWith('/admin')) return true

//   const authStore = useAdminAuthStore()
//   adminAuthReady ??= authStore.init()
//   await adminAuthReady

//   const isLoggedIn = !!authStore.session
//   if (to.name === 'admin-login') {
//     return isLoggedIn ? { name: 'admin-dashboard' } : true
//   }
//   return isLoggedIn ? true : { name: 'admin-login' }
// })

export default router
