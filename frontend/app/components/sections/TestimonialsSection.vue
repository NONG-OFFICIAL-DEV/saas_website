<template>
  <section v-if="store.testimonials.length" class="section-pad section-tint-sky" id="testimonials">
    <v-container>
      <div class="text-center mb-12" data-aos="fade-up">
        <span class="section-tag">{{ t('testimonials.eyebrow') }}</span>
        <h2 class="section-title">{{ t('testimonials.title') }}</h2>
      </div>

      <div class="testimonials-grid">
        <div
          v-for="(item, i) in store.testimonials"
          :key="item.id"
          class="testimonial-card clay-surface"
          data-aos="fade-up"
          :data-aos-delay="(i % 3) * 80"
        >
          <div v-if="item.rating" class="stars">
            <v-icon v-for="n in 5" :key="n" icon="mdi-star" size="16" :color="n <= item.rating ? 'warning' : undefined" />
          </div>

          <p class="quote">"{{ item.quote }}"</p>

          <div class="author-row">
            <v-avatar size="40" color="primary">
              <v-img v-if="item.author_avatar_url" :src="item.author_avatar_url" :alt="item.author_name" />
              <span v-else class="author-initial">{{ item.author_name?.charAt(0) }}</span>
            </v-avatar>
            <div class="author-text">
              <div class="author-name">{{ item.author_name }}</div>
              <div v-if="item.author_title" class="author-title">{{ item.author_title }}</div>
            </div>
          </div>
        </div>
      </div>
    </v-container>
  </section>
</template>

<script setup lang="ts">
  // Data is fetched by the parent page (app/pages/index.vue) via an awaited
  // useAsyncData call, so it's present in the server-rendered HTML — this
  // component only ever reads the store reactively.
  const { t } = useI18n()
  const store = useTestimonialsStore()
</script>

<style scoped>
  .testimonials-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
  }

  .testimonial-card {
    display: flex;
    flex-direction: column;
    gap: 16px;
    padding: 28px 26px;
  }

  .stars {
    display: flex;
    gap: 2px;
  }

  .quote {
    font-size: 0.92rem;
    line-height: 1.65;
    color: rgba(var(--v-theme-on-surface), 0.8);
    margin: 0;
    flex-grow: 1;
  }

  .author-row {
    display: flex;
    align-items: center;
    gap: 12px;
  }
  .author-initial {
    color: white;
    font-weight: 700;
  }
  .author-text {
    min-width: 0;
  }
  .author-name {
    font-size: 0.86rem;
    font-weight: 800;
  }
  .author-title {
    font-size: 0.76rem;
    color: rgba(var(--v-theme-on-surface), 0.55);
  }

  @media (max-width: 900px) {
    .testimonials-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
