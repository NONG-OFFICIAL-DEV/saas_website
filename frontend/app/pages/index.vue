<template>
  <!-- Pure content — layout (navbar/footer) is handled by the default layout -->
  <HeroSection />
  <ProductsTeaserSection />
  <BenefitsSection />
  <TestimonialsSection />
  <CtaSection />
</template>

<script setup lang="ts">
  // Fetched here (the page), not inside each section component — so the
  // homepage's real content is present in the server-rendered HTML instead
  // of only appearing after client hydration. The child sections just read
  // these stores reactively.
  const siteContentStore = useSiteContentStore()
  const productsStore = useProductsStore()
  const testimonialsStore = useTestimonialsStore()

  await useAsyncData('home-hero', async () => {
    await siteContentStore.fetchHero()
    return true
  })
  await useAsyncData('home-products-teaser', async () => {
    await productsStore.fetchProducts()
    return true
  })
  await useAsyncData('home-testimonials', async () => {
    await testimonialsStore.fetchTestimonials()
    return true
  })
</script>
