<template>
    <div class="alert">
      <v-slide-x-reverse-transition group>
        <v-alert
          v-for="alert in alerts"
          :key="alert.id"
          :type="alert.type"
          :icon="alert.icon"
          :dense="alert.dense"
          :prominent="alert.prominent"
          :dismissible="alert.dismissible"
          @input="closeAlert(alert.id)"
          closable
          >
          <!-- variant="outlined" -->
          <strong class="capitalize-first-letter">{{
            alert.message
          }}</strong>
        </v-alert>
      </v-slide-x-reverse-transition>
    </div>
  </template>

  <script lang="ts">
  import { defineComponent } from 'vue'

  type AlertType = 'error' | 'info' | 'warning' | 'success'

  interface Alert {
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
    props: {
      // Type d'affichage
      outlined: { type: Boolean, required: false, default: true },
      text: { type: Boolean, required: false, default: false },
      dense: { type: Boolean, required: false, default: false },
      prominent: { type: Boolean, required: false, default: false },
      // Ajout de la possibilité de fermer l'alerte
      dismissible: { type: Boolean, required: false, default: false },
      // Temps d'affichage à l'écran en ms
      defaultTimeout: { type: Number, required: false, default: 2000 },
      // Maximum d'alerte afficher en même Temps
      defaultMaxAlert: { type: Number, required: false, default: 4 },
    },
    data() {
      return {
        alerts: [] as Alert[],
      }
    },
    methods: {
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
        // Création d'un id unique
        const id = new Date().valueOf() + Math.random()
        this.alerts.push({
          id,
          type,
          icon,
          message,
          dense,
          prominent,
          dismissible,
        })
        // Si timeout = 0 on laisse l'alerte à l'écran
        if (timeout) {
          setTimeout(() => {
            this.closeAlert(id)
          }, timeout)
        }
      },
      closeAlert(id: number) {
        this.alerts = this.alerts.filter((el) => el.id !== id)
      },
    },
  })
  </script>

  <style scoped>
  .alert {
    overflow: hidden;
    position: fixed;
    top: 80px;
    right: 0;
    margin-right: 16px;
    z-index: 9999;
  }
  </style>
