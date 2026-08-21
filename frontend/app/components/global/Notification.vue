<template>
  <div class="alert fixed right-0 top-20 z-[9999] mr-4 flex flex-col gap-2">
    <TransitionGroup name="alert-slide">
      <Alert
        v-for="alert in alerts"
        :key="alert.id"
        :variant="alert.type === 'error' ? 'destructive' : 'default'"
        :class="typeClass(alert.type)"
        class="w-80 shadow-lg"
      >
        <Icon v-if="alert.icon" :name="alert.icon" :size="18" />
        <AlertDescription>
          <strong class="capitalize-first-letter">{{ alert.message }}</strong>
        </AlertDescription>
        <button
          type="button"
          class="absolute right-2 top-2 opacity-60 hover:opacity-100"
          aria-label="Dismiss"
          @click="closeAlert(alert.id)"
        >
          <Icon name="mdi-close" :size="16" />
        </button>
      </Alert>
    </TransitionGroup>
  </div>
</template>

<script lang="ts">
  import { defineComponent } from 'vue'
  import { Alert, AlertDescription } from '~/components/ui/alert'

  type AlertType = 'error' | 'info' | 'warning' | 'success'

  interface AlertItem {
    id: number
    type: AlertType
    icon: string | undefined
    message: string
    dense: boolean
    prominent: boolean
    dismissible: boolean
  }

  export default defineComponent({
    name: 'NotificationAlert',
    components: { Alert, AlertDescription },
    props: {
      outlined: { type: Boolean, required: false, default: true },
      text: { type: Boolean, required: false, default: false },
      dense: { type: Boolean, required: false, default: false },
      prominent: { type: Boolean, required: false, default: false },
      dismissible: { type: Boolean, required: false, default: false },
      // Time on screen in ms
      defaultTimeout: { type: Number, required: false, default: 2000 },
      // Max toasts shown at once
      defaultMaxAlert: { type: Number, required: false, default: 4 }
    },
    data() {
      return {
        alerts: [] as AlertItem[]
      }
    },
    methods: {
      typeClass(type: AlertType) {
        if (type === 'success') return 'border-success/30 bg-success/10 text-success [&_*[data-slot=alert-description]]:text-success'
        if (type === 'warning') return 'border-warning/30 bg-warning/10 text-warning [&_*[data-slot=alert-description]]:text-warning'
        if (type === 'info') return 'border-info/30 bg-info/10 text-info [&_*[data-slot=alert-description]]:text-info'
        return ''
      },
      newAlert(
        message: string,
        options: {
          type?: AlertType
          icon?: string
          timeout?: number
          dense?: boolean
          prominent?: boolean
          dismissible?: boolean
        } = {}
      ) {
        const type = options.type ?? 'success'
        const icon = options.icon
        const timeout = options.timeout ?? this.defaultTimeout
        const dense = options.dense ?? this.dense
        const prominent = options.prominent ?? this.prominent
        const dismissible = options.dismissible ?? this.dismissible

        if (this.alerts.length === this.defaultMaxAlert) this.alerts.shift()
        const id = new Date().valueOf() + Math.random()
        this.alerts.push({
          id,
          type,
          icon,
          message,
          dense,
          prominent,
          dismissible
        })
        if (timeout) {
          setTimeout(() => {
            this.closeAlert(id)
          }, timeout)
        }
      },
      closeAlert(id: number) {
        this.alerts = this.alerts.filter((el) => el.id !== id)
      }
    }
  })
</script>

<style scoped>
  .alert-slide-enter-active,
  .alert-slide-leave-active {
    transition: all 0.25s ease;
  }
  .alert-slide-enter-from,
  .alert-slide-leave-to {
    opacity: 0;
    transform: translateX(24px);
  }
</style>
