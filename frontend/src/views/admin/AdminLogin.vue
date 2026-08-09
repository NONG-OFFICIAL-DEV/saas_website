<template>
  <div class="login-shell">
    <v-card class="login-card" rounded="lg" elevation="0">
      <div class="login-logo">Nexstack <span>Admin</span></div>
      <p class="login-sub">Sign in to manage products.</p>

      <v-form @submit.prevent="handleSubmit">
        <v-alert
          v-if="authStore.error"
          type="error"
          variant="tonal"
          density="compact"
          rounded="lg"
          class="mb-4"
        >
          {{ authStore.error }}
        </v-alert>

        <v-text-field
          v-model="email"
          label="Email"
          type="email"
          autocomplete="username"
          required
          class="mb-2"
        />
        <v-text-field
          v-model="password"
          label="Password"
          type="password"
          autocomplete="current-password"
          required
          class="mb-4"
        />

        <v-btn
          color="primary"
          variant="flat"
          rounded="lg"
          block
          
          type="submit"
          :loading="authStore.loading"
        >
          Sign in
        </v-btn>
      </v-form>
    </v-card>
  </div>
</template>

<script setup>
  import { ref } from 'vue'
  import { useRouter } from 'vue-router'
  import { useAdminAuthStore } from '@/stores/adminAuth'

  const router = useRouter()
  const authStore = useAdminAuthStore()

  const email = ref('')
  const password = ref('')

  async function handleSubmit() {
    const ok = await authStore.signIn(email.value, password.value)
    if (ok) router.push({ name: 'admin-dashboard' })
  }
</script>

<style scoped>
  .login-shell {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 24px;
    background: rgb(var(--v-theme-background));
  }
  .login-card {
    width: 100%;
    max-width: 380px;
    padding: 36px 32px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  }
  .login-logo {
    font-size: 1.2rem;
    font-weight: 800;
    margin-bottom: 4px;
  }
  .login-logo span {
    color: rgb(var(--v-theme-primary));
  }
  .login-sub {
    font-size: 0.86rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin: 0 0 24px;
  }
</style>
