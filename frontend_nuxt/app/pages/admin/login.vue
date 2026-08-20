<template>
  <div class="login-shell">
    <div class="login-glow login-glow--a" />
    <div class="login-glow login-glow--b" />

    <v-card class="login-card" rounded="xl" elevation="0">
      <div class="login-brand">
        <v-avatar class="login-avatar" size="52" color="primary" variant="flat">
          <v-icon icon="mdi-view-dashboard-outline" size="26" />
        </v-avatar>
        <div class="login-logo">Nexstack <span>Admin</span></div>
        <p class="login-sub">Sign in to manage products and site content.</p>
      </div>

      <v-form ref="formRef" v-model="formValid" validate-on="submit lazy" @submit.prevent="handleSubmit">
        <transition name="fade">
          <v-alert
            v-if="authStore.error"
            type="error"
            variant="tonal"
            density="compact"
            rounded="lg"
            class="mb-4"
            icon="mdi-alert-circle-outline"
          >
            {{ authStore.error }}
          </v-alert>
        </transition>

        <v-text-field
          v-model="email"
          label="Email"
          type="email"
          autocomplete="username"
          autofocus
          prepend-inner-icon="mdi-email-outline"
          :rules="emailRules"
          :disabled="authStore.loading"
          class="mb-2"
          @update:model-value="authStore.error = null"
        />
        <v-text-field
          v-model="password"
          label="Password"
          :type="showPassword ? 'text' : 'password'"
          prepend-inner-icon="mdi-lock-outline"
          :append-inner-icon="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'"
          autocomplete="current-password"
          :rules="passwordRules"
          :disabled="authStore.loading"
          class="mb-5"
          @click:append-inner="showPassword = !showPassword"
          @update:model-value="authStore.error = null"
        />

        <v-btn
          color="primary"
          variant="flat"
          rounded="lg"
          size="large"
          block
          type="submit"
          append-icon="mdi-arrow-right"
          :loading="authStore.loading"
          :disabled="authStore.loading"
        >
          Sign in
        </v-btn>
      </v-form>
    </v-card>

    <p class="login-footer">Nexstack CMS — admin access only</p>
  </div>
</template>

<script setup lang="ts">
  definePageMeta({ layout: false })

  const authStore = useAdminAuthStore()

  const formRef = ref<{ validate: () => Promise<{ valid: boolean }> } | null>(null)
  const formValid = ref(false)
  const email = ref('')
  const password = ref('')
  const showPassword = ref(false)

  const emailRules = [
    (v: string) => !!v || 'Email is required',
    (v: string) => /.+@.+\..+/.test(v) || 'Enter a valid email address'
  ]
  const passwordRules = [(v: string) => !!v || 'Password is required']

  async function handleSubmit() {
    const { valid } = await formRef.value!.validate()
    if (!valid) return

    const ok = await authStore.signIn(email.value, password.value)
    if (ok) navigateTo('/admin')
  }
</script>

<style scoped>
  .login-shell {
    position: relative;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 20px;
    padding: 24px;
    background: rgb(var(--v-theme-background));
    overflow: hidden;
  }

  .login-glow {
    position: absolute;
    width: 480px;
    height: 480px;
    border-radius: 50%;
    background: rgb(var(--v-theme-primary));
    opacity: 0.16;
    filter: blur(120px);
    pointer-events: none;
  }
  .login-glow--a {
    top: -160px;
    left: -140px;
  }
  .login-glow--b {
    bottom: -180px;
    right: -140px;
    opacity: 0.12;
  }

  .login-card {
    position: relative;
    z-index: 1;
    width: 100%;
    max-width: 400px;
    padding: 40px 36px;
    border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
    background: rgba(var(--v-theme-surface), 0.72);
    backdrop-filter: blur(20px);
    box-shadow: 0 24px 60px -20px rgba(0, 0, 0, 0.35);
  }

  .login-brand {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    margin-bottom: 28px;
  }
  .login-avatar {
    margin-bottom: 14px;
  }
  .login-logo {
    font-size: 1.25rem;
    font-weight: 800;
    letter-spacing: -0.01em;
  }
  .login-logo span {
    color: rgb(var(--v-theme-primary));
  }
  .login-sub {
    font-size: 0.85rem;
    color: rgba(var(--v-theme-on-surface), 0.6);
    margin: 6px 0 0;
  }

  .login-footer {
    position: relative;
    z-index: 1;
    font-size: 0.76rem;
    color: rgba(var(--v-theme-on-surface), 0.4);
    margin: 0;
  }

  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.15s ease;
  }
  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
</style>
