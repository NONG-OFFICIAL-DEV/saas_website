<template>
  <!-- Desktop: permanent fixed sidebar -->
  <aside v-if="!mobile" class="admin-sidebar">
    <div class="drawer-logo">
      Nexstack <span class="drawer-logo-tag">Admin</span>
    </div>

    <nav class="drawer-list">
      <NuxtLink
        v-for="item in navItems"
        :key="item.to"
        :to="item.to"
        class="drawer-item"
        :class="{ active: item.match(route.path) }"
      >
        <Icon :name="item.icon" size="20" />
        {{ item.title }}
      </NuxtLink>
    </nav>

    <div class="drawer-footer">
      <Button as="a" variant="ghost" size="sm" class="w-full" href="/" target="_blank">
        <Icon name="mdi-open-in-new" size="16" />
        View site
      </Button>
      <Button variant="secondary" size="sm" class="w-full mt-2" @click="handleLogout">
        Log out
      </Button>
    </div>
  </aside>

  <!-- Mobile: overlay sheet -->
  <Sheet v-else v-model:open="drawer">
    <SheetContent side="left" class="admin-sidebar admin-sidebar--sheet">
      <div class="drawer-logo">
        Nexstack <span class="drawer-logo-tag">Admin</span>
      </div>

      <nav class="drawer-list">
        <NuxtLink
          v-for="item in navItems"
          :key="item.to"
          :to="item.to"
          class="drawer-item"
          :class="{ active: item.match(route.path) }"
          @click="onNavClick"
        >
          <Icon :name="item.icon" size="20" />
          {{ item.title }}
        </NuxtLink>
      </nav>

      <div class="drawer-footer">
        <Button as="a" variant="ghost" size="sm" class="w-full" href="/" target="_blank">
          <Icon name="mdi-open-in-new" size="16" />
          View site
        </Button>
        <Button variant="secondary" size="sm" class="w-full mt-2" @click="handleLogout">
          Log out
        </Button>
      </div>
    </SheetContent>
  </Sheet>

  <header v-if="mobile" class="admin-topbar">
    <Button variant="ghost" size="icon" @click="drawer = !drawer">
      <Icon name="mdi-menu" size="22" />
    </Button>
    <span class="admin-topbar-title">Nexstack Admin</span>
  </header>

  <main class="admin-main" :class="{ 'admin-main--mobile': mobile }">
    <Container fluid class="p-4">
      <slot />
    </Container>
  </main>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'
  import { Sheet, SheetContent } from '~/components/ui/sheet'

  const { mobile } = useAdminBreakpoint()
  const route = useRoute()
  const authStore = useAdminAuthStore()

  const drawer = ref(!mobile.value)

  // Keep the drawer's open/closed default in sync with viewport size —
  // e.g. resizing from desktop to mobile shouldn't leave a permanent
  // drawer forced open as a full-screen overlay.
  watch(mobile, (isMobile) => {
    drawer.value = !isMobile
  })

  // Explicit match functions instead of NuxtLink's own active-class
  // prefix matching, since e.g. a Products link at /admin was showing
  // "active" on every admin page (including /admin/site-content).
  const navItems = [
    {
      to: '/admin',
      icon: 'mdi-view-dashboard-outline',
      title: 'Products',
      match: (path: string) => path === '/admin' || path.startsWith('/admin/products')
    },
    {
      to: '/admin/solutions',
      icon: 'mdi-lightbulb-outline',
      title: 'Solutions',
      match: (path: string) => path.startsWith('/admin/solutions')
    },
    {
      to: '/admin/testimonials',
      icon: 'mdi-comment-quote-outline',
      title: 'Testimonials',
      match: (path: string) => path.startsWith('/admin/testimonials')
    },
    {
      to: '/admin/blog',
      icon: 'mdi-newspaper-variant-outline',
      title: 'Blog',
      match: (path: string) => path.startsWith('/admin/blog')
    },
    {
      to: '/admin/documentation/categories',
      icon: 'mdi-book-open-outline',
      title: 'Doc Categories',
      match: (path: string) => path.startsWith('/admin/documentation/categories')
    },
    {
      to: '/admin/documentation/articles',
      icon: 'mdi-file-document-outline',
      title: 'Doc Articles',
      match: (path: string) => path.startsWith('/admin/documentation/articles')
    },
    {
      to: '/admin/site-content',
      icon: 'mdi-file-document-edit-outline',
      title: 'Site Content',
      match: (path: string) => path === '/admin/site-content'
    },
    {
      to: '/admin/onboarding',
      icon: 'mdi-account-arrow-right-outline',
      title: 'Onboarding',
      match: (path: string) => path === '/admin/onboarding'
    }
  ]

  // On mobile the drawer is a temporary overlay — close it after picking
  // a destination so it doesn't sit on top of the page you just navigated to.
  function onNavClick() {
    if (mobile.value) drawer.value = false
  }

  async function handleLogout() {
    await authStore.signOut()
    navigateTo('/admin/login')
  }
</script>

<style scoped>
  .admin-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    width: 240px;
    display: flex;
    flex-direction: column;
    background: var(--card);
    border-right: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    z-index: 200;
  }
  .admin-sidebar--sheet {
    width: 240px;
  }

  .drawer-logo {
    padding: 20px 16px 12px;
    font-weight: 800;
    font-size: 1rem;
  }
  .drawer-logo-tag {
    color: var(--primary);
  }
  .drawer-list {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 2px;
    padding: 0 8px;
    overflow-y: auto;
  }
  .drawer-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 8px;
    font-size: 0.86rem;
    font-weight: 600;
    color: color-mix(in srgb, var(--foreground) 70%, transparent);
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
  }
  .drawer-item:hover {
    background: color-mix(in srgb, var(--foreground) 5%, transparent);
  }
  .drawer-item.active {
    background: color-mix(in srgb, var(--primary) 10%, transparent);
    color: var(--primary);
  }
  .drawer-footer {
    padding: 12px;
  }

  .admin-topbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    height: 56px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 12px;
    background: var(--card);
    border-bottom: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    z-index: 100;
  }
  .admin-topbar-title {
    font-weight: 800;
    font-size: 0.95rem;
  }

  .admin-main {
    margin-left: 240px;
  }
  .admin-main--mobile {
    margin-left: 0;
    padding-top: 56px;
  }
</style>
