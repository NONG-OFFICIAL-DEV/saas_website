<template>
  <section class="section-pad">
    <v-container>
      <v-row align="center">
        <v-col cols="12" md="4" class="text-center text-md-left avatar-col" data-aos="fade-right">
          <div class="avatar-geo d-none d-sm-block">
            <Geometric3D />
          </div>
          <v-avatar size="160" color="primary" class="about-avatar">
            <v-icon icon="mdi-account" size="80" color="white" />
          </v-avatar>
        </v-col>

        <v-col cols="12" md="8" data-aos="fade-left">
          <span class="section-tag">{{ about.tag }}</span>
          <h1 class="section-title">{{ about.greeting }}</h1>
          <p class="section-sub about-lead">
            {{ about.bio }}
          </p>

          <div class="about-links">
            <v-btn
              color="primary"
              rounded="lg"
              variant="flat"
              to="/products"
              append-icon="mdi-arrow-right"
            >
              {{ about.cta_label }}
            </v-btn>
            <v-btn
              rounded="lg"
              variant="outlined"
              :href="`mailto:${about.email}`"
              prepend-icon="mdi-email-outline"
            >
              {{ about.email }}
            </v-btn>
          </div>
        </v-col>
      </v-row>
    </v-container>
  </section>

  <section class="section-pad bg-soft">
    <v-container>
      <div class="text-center mb-10" data-aos="fade-up">
        <span class="section-tag">{{ about.how_tag }}</span>
        <h2 class="section-title">{{ about.how_title }}</h2>
      </div>

      <div class="values-grid">
        <template v-for="(v, i) in about.values" :key="v.title">
          <div class="value-card" data-aos="fade-up" :data-aos-delay="i * 100">
            <span class="value-num">{{ String(i + 1).padStart(2, '0') }}</span>
            <v-icon :icon="v.icon" size="24" color="primary" />
            <h3>{{ v.title }}</h3>
            <p>{{ v.description }}</p>
          </div>
          <div v-if="i < about.values.length - 1" class="value-connector" aria-hidden="true" />
        </template>
      </div>
    </v-container>
  </section>

  <section class="section-pad">
    <v-container class="text-center">
      <h2 class="section-title" data-aos="fade-up">{{ about.talk_title }}</h2>
      <p class="section-sub about-lead mx-auto" data-aos="fade-up">
        {{ about.talk_description }}
      </p>
      <div class="social-row" data-aos="fade-up">
        <a
          v-for="social in about.socials"
          :key="social.name"
          :href="social.href"
          :aria-label="social.name"
          class="social-btn"
          target="_blank"
          rel="noopener"
        >
          {{ social.name }}
        </a>
      </div>
    </v-container>
  </section>
</template>

<script setup>
  import { onMounted } from 'vue'
  import { storeToRefs } from 'pinia'
  import { useSiteContentStore } from '@/stores/siteContent'
  import Geometric3D from '@/components/ui/Geometric3D.vue'

  const store = useSiteContentStore()
  const { about } = storeToRefs(store)

  onMounted(() => store.fetchAbout())
</script>

<style scoped>
  .avatar-col {
    position: relative;
  }
  .avatar-geo {
    position: absolute;
    inset: -60px -40px auto auto;
    width: 220px;
    height: 220px;
    z-index: 0;
    opacity: 0.7;
    pointer-events: none;
  }
  .about-avatar {
    position: relative;
    z-index: 1;
    box-shadow: 0 16px 40px rgba(var(--v-theme-primary), 0.25);
  }
  .about-lead {
    max-width: 560px;
  }
  .about-links {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 22px;
  }

  .values-grid {
    display: flex;
    align-items: stretch;
    gap: 16px;
  }
  .value-connector {
    flex: 0 0 32px;
    align-self: center;
    height: 2px;
    margin-top: -40px;
    background: linear-gradient(
      90deg,
      rgba(var(--v-theme-primary), 0.35),
      rgba(var(--v-theme-primary), 0.08)
    );
  }
  .value-card {
    position: relative;
    flex: 1 1 220px;
    padding: 26px;
    border-radius: 16px;
    background: rgba(var(--v-theme-surface), 0.9);
    border: 1px solid rgba(var(--v-theme-on-surface), 0.07);
    text-align: center;
    overflow: hidden;
  }
  .value-num {
    position: absolute;
    top: 8px;
    right: 16px;
    font-size: 2.2rem;
    font-weight: 900;
    line-height: 1;
    color: rgba(var(--v-theme-primary), 0.1);
  }

  @media (max-width: 768px) {
    .values-grid {
      flex-direction: column;
    }
    .value-connector {
      display: none;
    }
  }
  .value-card h3 {
    font-size: 1rem;
    font-weight: 800;
    margin: 14px 0 6px;
  }
  .value-card p {
    font-size: 0.86rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    line-height: 1.55;
    margin: 0;
  }

  .social-row {
    display: flex;
    justify-content: center;
    gap: 12px;
    flex-wrap: wrap;
    margin-top: 20px;
  }
  .social-btn {
    padding: 10px 20px;
    border-radius: 999px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
    color: rgb(var(--v-theme-on-surface));
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition:
      background 0.15s,
      color 0.15s;
  }
  .social-btn:hover {
    background: rgba(var(--v-theme-primary), 0.1);
    color: rgb(var(--v-theme-primary));
  }
</style>
