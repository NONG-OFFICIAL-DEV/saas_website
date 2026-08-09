<template>
  <div class="step-account pa-4">
    <v-form ref="formRef" @submit.prevent="handleSubmit">
      <!-- Name row -->
      <div class="form-row">
        <div class="form-group">
          <label class="field-label">{{ t('auth.register.first_name') }}</label>
          <v-text-field
            v-model="form.firstName"
            :rules="[rules.required]"
            :error-messages="serverError('owner_first_name')"
            :placeholder="t('auth.register.first_name_placeholder')"
            prepend-inner-icon="mdi-account-outline"
            variant="outlined"
            density="comfortable"
            rounded="lg"
          />
        </div>
        <div class="form-group">
          <label class="field-label">{{ t('auth.register.last_name') }}</label>
          <v-text-field
            v-model="form.lastName"
            :rules="[rules.required]"
            :error-messages="serverError('owner_last_name')"
            :placeholder="t('auth.register.last_name_placeholder')"
            prepend-inner-icon="mdi-account-outline"
            variant="outlined"
            density="comfortable"
            rounded="lg"
          />
        </div>
      </div>

      <!-- Email -->
      <div class="form-group">
        <label class="field-label">{{ t('auth.register.email') }}</label>
        <v-text-field
          v-model="form.email"
          :rules="[rules.required, rules.email]"
          :error-messages="serverError('owner_email')"
          :placeholder="t('auth.register.email_placeholder')"
          prepend-inner-icon="mdi-email-outline"
          variant="outlined"
          density="comfortable"
          rounded="lg"
        />
      </div>

      <!-- Password row -->
      <div class="form-row">
        <div class="form-group">
          <label class="field-label">{{ t('auth.register.password') }}</label>
          <v-text-field
            v-model="form.password"
            :rules="[rules.required, rules.minLength]"
            :error-messages="serverError('owner_password')"
            :type="showPw ? 'text' : 'password'"
            :placeholder="t('auth.register.password_placeholder')"
            prepend-inner-icon="mdi-lock-outline"
            :append-inner-icon="
              showPw ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
            "
            variant="outlined"
            density="comfortable"
            rounded="lg"
            @click:append-inner="showPw = !showPw"
          />
        </div>
        <div class="form-group">
          <label class="field-label">
            {{ t('auth.register.confirm_password') }}
          </label>
          <v-text-field
            v-model="form.confirmPassword"
            :rules="[rules.required, rules.matchPw]"
            :type="showCpw ? 'text' : 'password'"
            :placeholder="t('auth.register.confirm_password_placeholder')"
            prepend-inner-icon="mdi-lock-check-outline"
            :append-inner-icon="
              showCpw ? 'mdi-eye-off-outline' : 'mdi-eye-outline'
            "
            variant="outlined"
            density="comfortable"
            rounded="lg"
            @click:append-inner="showCpw = !showCpw"
          />
        </div>
      </div>

      <!-- Password strength indicator -->
      <div v-if="form.password" class="pw-strength">
        <div class="pw-bars">
          <div
            v-for="i in 4"
            :key="i"
            class="pw-bar"
            :class="pwStrengthClass(i)"
          />
        </div>
        <span class="pw-label" :class="`pw-label--${pwLevel}`">
          {{ t(`auth.register.pw_strength.${pwLevel}`) }}
        </span>
      </div>

      <!-- Terms -->
      <v-checkbox
        v-model="form.agreed"
        :rules="[rules.mustAgree]"
        color="primary"
        density="compact"
        class="terms-check"
      >
        <template #label>
          <span class="terms-label">
            {{ t('auth.register.agree_prefix') }}
            <a href="/terms" target="_blank" class="link-primary">
              {{ t('auth.register.terms') }}
            </a>
            {{ t('auth.register.and') }}
            <a href="/privacy" target="_blank" class="link-primary">
              {{ t('auth.register.privacy') }}
            </a>
          </span>
        </template>
      </v-checkbox>

      <!-- Global API error -->
      <v-alert
        v-if="apiError && typeof apiError === 'string'"
        type="error"
        variant="tonal"
        rounded="lg"
        density="compact"
        :text="apiError"
      />

      <!-- Actions -->
      <div class="sub-actions">
        <v-btn
          variant="outlined"
          rounded="lg"
          
          prepend-icon="mdi-arrow-left"
          @click="emit('back')"
        >
          {{ t('auth.register.back') }}
        </v-btn>
        <v-btn
          type="submit"
          color="primary"
          
          rounded="lg"
          :loading="loading"
          append-icon="mdi-check"
          class="submit-btn px-8"
        >
          {{ t('auth.register.submit') }}
        </v-btn>
      </div>

      <p class="secure-note">
        <v-icon icon="mdi-lock-outline" size="12" />
        {{ t('auth.register.secure_note') }}
      </p>
    </v-form>
  </div>
</template>

<script setup>
  import { ref, computed } from 'vue'
  import { useI18n } from 'vue-i18n'
  import { useRegisterStore } from '@/stores/register'

  const { t } = useI18n()
  const store = useRegisterStore()

  const props = defineProps({
    form: { type: Object, required: true },
    loading: { type: Boolean, default: false },
    apiError: { type: [String, Object, null], default: null }
  })
  const emit = defineEmits(['back', 'submit'])

  const formRef = ref(null)
  const showPw = ref(false)
  const showCpw = ref(false)

  const rules = {
    required: v => !!v || t('auth.validation.required'),
    email: v => /.+@.+\..+/.test(v) || t('auth.validation.email'),
    minLength: v => v?.length >= 8 || t('auth.validation.min8'),
    matchPw: v => v === props.form.password || t('auth.validation.pw_match'),
    mustAgree: v => !!v || t('auth.validation.agree')
  }

  function serverError(field) {
    return store.fieldError(field) ?? undefined
  }

  // Password strength
  const pwScore = computed(() => {
    const p = props.form.password || ''
    let s = 0
    if (p.length >= 6) s++
    if (p.length >= 10) s++
    if (/[A-Z]/.test(p) && /[0-9]/.test(p)) s++
    if (/[^A-Za-z0-9]/.test(p)) s++
    return s
  })
  const pwLevel = computed(() => {
    return ['weak', 'fair', 'good', 'strong'][pwScore.value - 1] ?? 'weak'
  })

  function pwStrengthClass(i) {
    return i <= pwScore.value ? `filled-${pwLevel.value}` : 'empty'
  }

  async function handleSubmit() {
    const { valid } = await formRef.value.validate()
    if (!valid) return
    emit('submit')
  }
</script>

<style scoped>
  .step-account {
    display: flex;
    flex-direction: column;
    gap: 16px;
  }
  .form-row {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }
  @media (max-width: 500px) {
    .form-row {
      grid-template-columns: 1fr;
    }
  }

  .form-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }
  .field-label {
    font-size: 0.75rem;
    font-weight: 700;
    opacity: 0.55;
    display: flex;
    align-items: center;
    gap: 5px;
  }

  /* Password strength */
  .pw-strength {
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .pw-bars {
    display: flex;
    gap: 4px;
    flex: 1;
  }
  .pw-bar {
    height: 3px;
    flex: 1;
    border-radius: 99px;
    background: rgba(var(--v-theme-on-surface), 0.1);
    transition: background 0.2s;
  }
  .pw-bar.filled-weak {
    background: #ef4444;
  }
  .pw-bar.filled-fair {
    background: #f59e0b;
  }
  .pw-bar.filled-good {
    background: #3b82f6;
  }
  .pw-bar.filled-strong {
    background: #22c55e;
  }
  .pw-label {
    font-size: 0.7rem;
    font-weight: 700;
    white-space: nowrap;
  }
  .pw-label--weak {
    color: #ef4444;
  }
  .pw-label--fair {
    color: #f59e0b;
  }
  .pw-label--good {
    color: #3b82f6;
  }
  .pw-label--strong {
    color: #22c55e;
  }

  .terms-check {
    margin-top: -4px;
  }
  .terms-label {
    font-size: 0.7rem;
    color: rgba(var(--v-theme-on-surface), 0.65);
    line-height: 1.5;
  }

  .sub-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 4px;
    gap: 12px;
  }
  .submit-btn {
    box-shadow: 0 6px 20px rgba(var(--v-theme-primary), 0.28) !important;
  }

  .secure-note {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    font-size: 0.72rem;
    opacity: 0.5;
    margin: 4px 0 0;
  }

  .link-primary {
    color: rgb(var(--v-theme-primary));
    font-weight: 700;
    text-decoration: none;
  }
  .link-primary:hover {
    text-decoration: underline;
  }
</style>
