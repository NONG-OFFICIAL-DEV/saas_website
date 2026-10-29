<template>
  <div>
    <h1 class="page-title">Site Content</h1>
    <p class="page-sub">Edit the homepage hero, About page, and footer.</p>

    <div class="language-row">
      <Label class="language-label">Editing language</Label>
      <Tabs v-model="activeLocale">
        <TabsList>
          <TabsTrigger v-for="loc in LOCALES" :key="loc.code" :value="loc.code">
            {{ loc.label }}
          </TabsTrigger>
        </TabsList>
      </Tabs>
    </div>

    <Tabs v-model="tab" class="w-full">
      <TabsList class="mb-6">
        <TabsTrigger value="hero">Hero</TabsTrigger>
        <TabsTrigger value="about">About</TabsTrigger>
        <TabsTrigger value="footer">Footer</TabsTrigger>
      </TabsList>

      <TabsContent value="hero">
        <SiteContentHeroEditor :active-locale="activeLocale" :locales="LOCALES" />
      </TabsContent>

      <TabsContent value="about">
        <SiteContentAboutEditor :active-locale="activeLocale" :locales="LOCALES" />
      </TabsContent>

      <TabsContent value="footer">
        <SiteContentFooterEditor :active-locale="activeLocale" :locales="LOCALES" />
      </TabsContent>
    </Tabs>
  </div>
</template>

<script setup lang="ts">
// See pages/admin/index.vue for why every admin page opts out of i18n routing.
defineI18nRoute(false)
definePageMeta({ layout: 'admin' })

import { Label } from '~/components/ui/label'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '~/components/ui/tabs'

const tab = ref('hero')

const LOCALES = [
  { code: 'en', label: 'English' },
  { code: 'km', label: 'Khmer' }
]
const activeLocale = ref('en')
</script>

<style scoped>
.page-title {
  font-size: 1.5rem;
  font-weight: 800;
  margin: 0 0 4px;
}
.page-sub {
  font-size: 0.86rem;
  color: color-mix(in srgb, var(--foreground) 60%, transparent);
  margin: 0 0 24px;
}
.language-row {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-bottom: 20px;
  padding-bottom: 16px;
  border-bottom: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
}
.language-label {
  font-size: 0.82rem;
  font-weight: 700;
  color: color-mix(in srgb, var(--foreground) 60%, transparent);
}
</style>
