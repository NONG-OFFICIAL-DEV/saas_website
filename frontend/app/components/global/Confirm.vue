<template>
  <Dialog :open="dialog" @update:open="onOpenChange">
    <DialogContent class="w-[470px] max-w-[calc(100%-2rem)] p-0 gap-0" :show-close-button="false">
      <DialogHeader class="bg-destructive px-4 py-3 rounded-t-xl">
        <DialogTitle class="text-destructive-foreground">
          <strong>Confirm Deletion</strong>
        </DialogTitle>
      </DialogHeader>
      <DialogDescription v-show="!!message" class="capitalize-first-letter px-4 pt-6 pb-4" v-html="message" />
      <div class="flex justify-end gap-2 p-4">
        <Button variant="secondary" @click="cancel">
          {{ $t('button.cancel') }}
        </Button>
        <Button class="bg-destructive text-destructive-foreground hover:bg-destructive/90" @click="agree">
          {{ $t('button.yes') }}
        </Button>
      </div>
    </DialogContent>
  </Dialog>
</template>

<script lang="ts">
  import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '~/components/ui/dialog'
  import { Button } from '~/components/ui/button'

  interface ConfirmOptions {
    type?: string
    width?: number
    agreeBtnText?: string
    denyBtnText?: string
  }

  export default {
    name: 'ConfirmDialog',
    components: { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription, Button },
    data() {
      return {
        dialog: false,
        resolving: false,
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
        this.resolving = true
        if (this.agreeCallback) await this.agreeCallback()
        this.dialog = false
        this.resolving = false
      },
      async cancel() {
        this.resolving = true
        if (this.cancelCallback) await this.cancelCallback()
        this.dialog = false
        this.resolving = false
      },
      // Fires on Escape / outside-click too (Reka UI's Dialog), not just
      // the explicit Cancel button — the original Vuetify dialog only
      // wired Escape to cancel(), backdrop-click silently closed with no
      // callback. Treating outside-click the same as Escape here is a
      // minor, harmless behavior widening (cancelCallback defaults to a
      // no-op everywhere it's used), not a functional regression.
      onOpenChange(value: boolean) {
        this.dialog = value
        if (!value && !this.resolving) {
          this.cancelCallback?.()
        }
      }
    }
  }
</script>
