<template>
  <section class="hero-section section-tint-lavender">
    <!-- Decorative 3D illustrations -->
    <div class="hero-geo hero-geo--right hidden md:block">
      <Geometric3D />
    </div>
    <div class="hero-geo hero-geo--left hidden lg:block">
      <Geometric3D />
    </div>

    <Container>
      <div class="w-full md:w-9/12 lg:w-7/12 mx-auto text-center">

          <!-- Badge -->
          <div class="hero-badge mb-7" data-aos="fade-down" data-aos-delay="0">
            <span class="pulse-dot" />
            {{ hero.badge_text }}
          </div>

          <!-- Headline -->
          <h1 class="hero-title mb-6" data-aos="fade-up" data-aos-delay="100">
            {{ hero.headline }}<br />
            <span class="hero-title-sub">{{ hero.subheadline }}</span>
          </h1>

          <!-- Sub-headline -->
          <p class="hero-sub mb-10" data-aos="fade-up" data-aos-delay="200">
            {{ hero.description }}
          </p>

          <!-- CTA buttons -->
          <div class="flex gap-3 justify-center flex-wrap" data-aos="fade-up" data-aos-delay="300">
            <Button as="NuxtLink" to="/products" class="hero-btn px-10">
              {{ hero.cta_primary_label }}
              <Icon name="mdi-arrow-right" size="18" />
            </Button>
            <Button
              as="a"
              variant="outline"
              class="px-10"
              :href="hero.cta_secondary_url as string"
              target="_blank"
              rel="noopener"
            >
              {{ hero.cta_secondary_label }}
            </Button>
          </div>

          <!-- Trust line -->
          <p class="hero-trust-line mt-5" data-aos="fade-up" data-aos-delay="350">
            {{ hero.trust_line }}
          </p>

          <!-- Stats row -->
          <Row dense class="mt-16" data-aos="fade-up" data-aos-delay="400">
            <Col v-for="s in (hero.stats as { num: string; label: string }[])" :key="s.label" cols="6" sm="3">
              <div class="stat-pill">
                <div class="stat-num">{{ s.num }}</div>
                <div class="stat-label">{{ s.label }}</div>
              </div>
            </Col>
          </Row>

      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
  import { Button } from '~/components/ui/button'

  // Data is fetched by the parent page (app/pages/index.vue) via an awaited
  // useAsyncData call, so it's present in the server-rendered HTML — this
  // component only ever reads the store reactively.
  const store = useSiteContentStore()
  const { hero } = storeToRefs(store)
</script>

<style scoped>
.hero-section {
  position: relative;
  padding: 120px 0 90px;
  overflow: hidden;
}
.hero-geo {
  position: absolute;
  z-index: 0;
  pointer-events: none;
  opacity: 0.85;
}
.hero-geo--right {
  width: 260px;
  height: 260px;
  top: 40px;
  right: 0;
}
.hero-geo--left {
  width: 190px;
  height: 190px;
  bottom: 20px;
  left: -20px;
  transform: scaleX(-1);
  opacity: 0.6;
}

.hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: rgba(37, 99, 235, 0.08);
  border: 1px solid rgba(37, 99, 235, 0.18);
  color: var(--primary);
  font-size: 0.8rem;
  font-weight: 700;
  padding: 7px 18px;
  border-radius: 999px;
  letter-spacing: 0.3px;
}
.pulse-dot {
  width: 7px; height: 7px;
  background: var(--primary);
  border-radius: 50%;
  display: inline-block;
  animation: pulse 2s infinite;
}
@keyframes pulse {
  0%, 100% { transform: scale(1);   opacity: 1;   }
  50%       { transform: scale(1.6); opacity: 0.5; }
}

.hero-title {
   font-size: clamp(2.2rem, 5.5vw, 4rem);
  font-weight: 900;
  line-height: 1.08;
  letter-spacing: -2.5px;
  position: relative;
  z-index: 1;
}
.hero-title-sub {
  display: block;
  font-size: clamp(1.3rem, 3vw, 2.2rem);   /* ~60% of title size */
  font-weight: 700;
  letter-spacing: -0.8px;
  line-height: 1.25;
  margin-top: 8px;
  opacity: 0.55;                            /* muted, not gradient */
  -webkit-text-fill-color: unset;           /* cancels gradient-text if inherited */
  background: none;
}
.hero-sub {
  font-size: 1.15rem;
  max-width: 560px;
  margin: 0 auto;
  opacity: 0.65;
  line-height: 1.7;
  position: relative;
  z-index: 1;
}
.hero-btn {
  box-shadow: 0 8px 32px rgba(37, 99, 235, 0.35) !important;
}
.hero-trust-line {
  font-size: 0.78rem;
  font-weight: 600;
  opacity: 0.5;
  position: relative;
  z-index: 1;
}
.stat-pill {
  background: var(--card);
  border: 1px solid color-mix(in srgb, var(--foreground) 6%, transparent);
  border-radius: 18px;
  padding: 16px 8px;
  text-align: center;
  box-shadow: 0 8px 20px color-mix(in srgb, var(--foreground) 5%, transparent);
}
.stat-num {
  font-size: 1.9rem;
  font-weight: 900;
  letter-spacing: -1px;
  color: var(--primary);
}
.stat-label {
  font-size: 0.72rem;
  opacity: 0.6;
  margin-top: 3px;
  font-weight: 600;
}

@media (max-width: 768px) {
  .hero-section { padding: 80px 0 60px; }
  .hero-title    { letter-spacing: -1.5px; }
}
</style>
