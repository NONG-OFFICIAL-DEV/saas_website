import '@mdi/font/css/materialdesignicons.css'
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import { km, en } from 'vuetify/locale'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import * as labs from 'vuetify/labs/components'
import { VDateInput } from 'vuetify/labs/VDateInput'

const vuetify = createVuetify({
  components: {
    VDateInput,
    ...components,
    ...labs
  },
  directives,
  // 👉 DEFAULT PROPS
  defaults: {
    // Every button defaults to the same rounding site-wide, so a stray
    // component never needs to (or should) set `rounded` itself.
    VBtn: {
      rounded: 'lg'
    },

    VDateInput: {
      density: 'comfortable',
      variant: 'outlined',
      color: 'primary',
      prependIcon: '',
      appendInnerIcon: '$calendar',
      format: 'DD-MM-YYYY',
      hideActions: true
    },

    VSelect: {
      density: 'comfortable',
      variant: 'outlined',
      color: 'primary'
    },

    // Text field defaults
    VTextField: {
      variant: 'outlined',
      density: 'comfortable',
      color: 'primary'
    },

    // Textarea defaults
    VTextarea: {
      variant: 'outlined',
      density: 'comfortable',
      color: 'primary',
      autoGrow: true,
      rows: 3
    },

    // Autocomplete defaults
    VAutocomplete: {
      variant: 'outlined',
      density: 'comfortable',
      color: 'primary'
    },

    VDataTableServer: {
      class: 'rounded-lg'
    }
  },
  theme: {
    defaultTheme: localStorage.getItem('theme') === 'light' ? 'light' : 'dark',
    themes: {
      light: {
        dark: false,
        colors: {
          primary: '#3B5BDB', // Indigo Blue — main action color
          secondary: '#6C757D', // Neutral gray for secondary actions
          surface: '#FFFFFF',
          background: '#F8F9FA',
          success: '#099268',
          warning: '#F76707',
          error: '#C92A2A',
          info: '#1971C2'
        }
      },
      dark: {
        dark: true,
        colors: {
          primary: '#748FFC', // Lighter indigo — readable on dark bg
          secondary: '#ADB5BD',
          surface: '#1E1E2E',
          background: '#151521',
          success: '#2F9E44',
          warning: '#F59F00',
          error: '#E03131',
          info: '#228BE6'
        }
      }
    }
  },
  locale: {
    messages: { km, en },
    locale: 'en'
  },
  icons: {
    iconfont: 'mdi'
  },
  date: {
    locale: {
      en: 'en-GB'
    }
  }
})

export default vuetify
