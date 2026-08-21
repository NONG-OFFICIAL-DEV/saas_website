<template>
  <div class="login-shell">
    <div class="login-glow login-glow--a" />
    <div class="login-glow login-glow--b" />

    <Card class="login-card">
      <div class="login-brand">
        <Avatar class="login-avatar size-[52px] bg-primary">
          <AvatarFallback class="bg-primary text-primary-foreground">
            <Icon name="mdi-view-dashboard-outline" size="26" />
          </AvatarFallback>
        </Avatar>
        <div class="login-logo">Nexstack <span>Admin</span></div>
        <p class="login-sub">Sign in to manage products and site content.</p>
      </div>

      <form @submit.prevent="handleSubmit">
        <transition name="fade">
          <Alert v-if="authStore.error" variant="destructive" class="mb-4">
            <AlertDescription>{{ authStore.error }}</AlertDescription>
          </Alert>
        </transition>

        <div class="field mb-3">
          <Label for="email">Email</Label>
          <Input
            id="email"
            v-model="email"
            type="email"
            autocomplete="username"
            autofocus
            :disabled="authStore.loading"
            :class="emailError ? 'border-destructive' : ''"
            @update:model-value="authStore.error = null"
          />
          <p v-if="emailError" class="field-error">{{ emailError }}</p>
        </div>

        <div class="field mb-5">
          <Label for="password">Password</Label>
          <div class="password-row">
            <Input
              id="password"
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              autocomplete="current-password"
              :disabled="authStore.loading"
              :class="passwordError ? 'border-destructive' : ''"
              @update:model-value="authStore.error = null"
            />
            <button type="button" class="password-toggle" @click="showPassword = !showPassword">
              <Icon :name="showPassword ? 'mdi-eye-off-outline' : 'mdi-eye-outline'" size="18" />
            </button>
          </div>
          <p v-if="passwordError" class="field-error">{{ passwordError }}</p>
        </div>

        <Button type="submit" size="lg" class="w-full" :disabled="authStore.loading">
          <Icon v-if="authStore.loading" name="mdi-loading" size="16" class="animate-spin" />
          Sign in
          <Icon v-if="!authStore.loading" name="mdi-arrow-right" size="18" />
        </Button>
      </form>
    </Card>

    <p class="login-footer">Nexstack CMS — admin access only</p>
  </div>
</template>

<script setup lang="ts">
  import { Alert, AlertDescription } from '~/components/ui/alert'
  import { Avatar, AvatarFallback } from '~/components/ui/avatar'
  import { Button } from '~/components/ui/button'
  import { Card } from '~/components/ui/card'
  import { Input } from '~/components/ui/input'
  import { Label } from '~/components/ui/label'

  definePageMeta({ layout: false })

  const authStore = useAdminAuthStore()

  const email = ref('')
  const password = ref('')
  const showPassword = ref(false)
  const emailError = ref<string | null>(null)
  const passwordError = ref<string | null>(null)

  function validate() {
    emailError.value = !email.value
      ? 'Email is required'
      : !/.+@.+\..+/.test(email.value)
        ? 'Enter a valid email address'
        : null
    passwordError.value = !password.value ? 'Password is required' : null
    return !emailError.value && !passwordError.value
  }

  async function handleSubmit() {
    if (!validate()) return

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
    background: var(--background);
    overflow: hidden;
  }

  .login-glow {
    position: absolute;
    width: 480px;
    height: 480px;
    border-radius: 50%;
    background: var(--primary);
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
    border: 1px solid color-mix(in srgb, var(--foreground) 8%, transparent);
    background: color-mix(in srgb, var(--card) 72%, transparent);
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
    color: var(--primary);
  }
  .login-sub {
    font-size: 0.85rem;
    color: color-mix(in srgb, var(--foreground) 60%, transparent);
    margin: 6px 0 0;
  }

  .field {
    display: flex;
    flex-direction: column;
    gap: 6px;
  }
  .field-error {
    font-size: 0.75rem;
    color: var(--destructive);
    margin: 0;
  }
  .password-row {
    position: relative;
    display: flex;
  }
  .password-row :deep(input) {
    padding-right: 36px;
  }
  .password-toggle {
    position: absolute;
    top: 50%;
    right: 8px;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: color-mix(in srgb, var(--foreground) 55%, transparent);
    cursor: pointer;
    display: flex;
    align-items: center;
  }

  .login-footer {
    position: relative;
    z-index: 1;
    font-size: 0.76rem;
    color: color-mix(in srgb, var(--foreground) 40%, transparent);
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
