<template>
  <v-dialog v-model="dialog" :max-width="options.width" @keydown.esc="cancel">
    <v-card width="470">
      <v-card-title class="bg-red d-flex">
        <strong>Confirm Deletion</strong>
      </v-card-title>
      <v-card-text
        v-show="!!message"
        class="capitalize-first-letter pt-6 pb-4"
        v-html="message"
      />
      <v-card-actions class="pa-4">
        <v-spacer />
        <v-btn
          elevation="0"
          ref="btnNo"
          @click.native="cancel"
          variant="tonal"
        >
          {{ $t('button.cancel') }}
        </v-btn>
        <v-btn elevation="0" class="bg-red" @click.native="agree">
          {{ $t('button.yes') }}
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script lang="ts">
  interface ConfirmOptions {
    type?: string
    width?: number
    agreeBtnText?: string
    denyBtnText?: string
  }

  export default {
    name: 'ConfirmDialog',
    data() {
      return {
        dialog: false,
        agreeCallback: null as (() => void | Promise<void>) | null,
        cancelCallback: null as (() => void | Promise<void>) | null,
        message: null as string | null,
        title: null as string | null,
        options: {
          type: 'error',
          width: 290,
          agreeBtnText: this.$t('button.delete'),
          denyBtnText: this.$t('button.cancel')
        } as ConfirmOptions
      }
    },
    methods: {
      bgColor() {
        const colors: Record<string, string> = {
          info: '#233F740F',
          error: '#FF52520F',
          warning: '#FFC1070F'
        }

        return colors[this.options.type || 'info']
      },
      open({ title, message, options, agree = () => {}, cancel = () => {} }: {
        title?: string
        message?: string
        options?: ConfirmOptions
        agree?: () => void | Promise<void>
        cancel?: () => void | Promise<void>
      }) {
        this.dialog = true
        this.title = title ?? null
        this.message = message ?? null
        this.options = Object.assign(this.options, options)
        this.agreeCallback = agree
        this.cancelCallback = cancel
      },
      async agree() {
        if (this.agreeCallback) await this.agreeCallback()
        this.dialog = false
      },
      async cancel() {
        if (this.cancelCallback) await this.cancelCallback()
        this.dialog = false
      }
    }
  }
</script>
