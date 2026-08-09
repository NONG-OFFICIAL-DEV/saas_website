<template>
  <!-- Pure content — layout (navbar/footer) is handled by LandingLayout -->
  <HeroSection />
  <ProductsTeaserSection />
  <BenefitsSection />
  <CtaSection />
</template>

<script setup>
import HeroSection          from '@/components/sections/HeroSection.vue'
import ProductsTeaserSection from '@/components/sections/ProductsTeaserSection.vue'
import BenefitsSection      from '@/components/sections/BenefitsSection.vue'
import CtaSection           from '@/components/sections/CtaSection.vue'
</script>