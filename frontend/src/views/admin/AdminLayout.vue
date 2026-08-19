<template>
  <v-navigation-drawer v-model="drawer" :temporary="mobile" width="240">
    <div class="drawer-logo">
      Nexstack <span class="drawer-logo-tag">Admin</span>
    </div>

    <v-list nav density="comfortable" class="drawer-list">
      <v-list-item
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        :active="item.match(route.path)"
        :prepend-icon="item.icon"
        :title="item.title"
        rounded="lg"
        color="primary"
        @click="onNavClick"
      />
    </v-list>

    <template #append>
      <div class="drawer-footer">
        <v-btn
          variant="text"
          size="small"
          block
          href="/"
          target="_blank"
          prepend-icon="mdi-open-in-new"
        >
          View site
        </v-btn>
        <v-btn variant="tonal" size="small" block class="mt-2" @click="handleLogout">
          Log out
        </v-btn>
      </div>
    </template>
  </v-navigation-drawer>

  <v-app-bar v-if="mobile" density="comfortable" flat border>
    <v-app-bar-nav-icon @click="drawer = !drawer" />
    <v-app-bar-title>Nexstack Admin</v-app-bar-title>
  </v-app-bar>

  <v-main>
    <v-container fluid class="pa-4">
      <router-view />
    </v-container>
  </v-main>
</template>

<script setup>
  import { ref, watch } from 'vue'
  import { useDisplay } from 'vuetify'
  import { useRoute, useRouter } from 'vue-router'
  import { useAdminAuthStore } from '@/stores/adminAuth'

  const { mobile } = useDisplay()
  const route = useRoute()
  const router = useRouter()
  const authStore = useAdminAuthStore()

  const drawer = ref(!mobile.value)

  // Keep the drawer's open/closed default in sync with viewport size —
  // e.g. resizing from desktop to mobile shouldn't leave a permanent
  // drawer forced open as a full-screen overlay.
  watch(mobile, isMobile => {
    drawer.value = !isMobile
  })

  // Vuetify's v-list-item :to does prefix-based active matching by
  // default, so a Products link at /admin was showing "active" on every
  // admin page (including /admin/site-content). Match explicitly instead.
  const navItems = [
    {
      to: '/admin',
      icon: 'mdi-view-dashboard-outline',
      title: 'Products',
      match: path => path === '/admin' || path.startsWith('/admin/products')
    },
    {
      to: '/admin/solutions',
      icon: 'mdi-lightbulb-outline',
      title: 'Solutions',
      match: path => path.startsWith('/admin/solutions')
    },
    {
      to: '/admin/testimonials',
      icon: 'mdi-comment-quote-outline',
      title: 'Testimonials',
      match: path => path.startsWith('/admin/testimonials')
    },
    {
      to: '/admin/blog',
      icon: 'mdi-newspaper-variant-outline',
      title: 'Blog',
      match: path => path.startsWith('/admin/blog')
    },
    {
      to: '/admin/site-content',
      icon: 'mdi-file-document-edit-outline',
      title: 'Site Content',
      match: path => path === '/admin/site-content'
    }
  ]

  // On mobile the drawer is a temporary overlay — close it after picking
  // a destination so it doesn't sit on top of the page you just navigated to.
  function onNavClick() {
    if (mobile.value) drawer.value = false
  }

  async function handleLogout() {
    await authStore.signOut()
    router.push({ name: 'admin-login' })
  }
</script>

<style scoped>
  .drawer-logo {
    padding: 20px 16px 12px;
    font-weight: 800;
    font-size: 1rem;
  }
  .drawer-logo-tag {
    color: rgb(var(--v-theme-primary));
  }
  .drawer-list {
    padding: 0 8px;
  }
  .drawer-footer {
    padding: 12px;
  }
</style>
