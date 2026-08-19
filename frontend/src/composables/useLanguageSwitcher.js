import { computed, ref } from 'vue'
import { useI18n } from 'vue-i18n'

export function useLanguageSwitcher() {
  const { t, locale } = useI18n()

  const menuOpen = ref(false)

  const languages = computed(() => [
    {
      code: 'km',
      label: t('common.lang_km') || 'ខ្មែរ',
      imgSrc: 'https://flagcdn.com/w80/kh.png',
      alt: 'Khmer'
    },
    {
      code: 'en',
      label: t('common.lang_en') || 'English',
      imgSrc: 'https://flagcdn.com/w80/gb.png',
      alt: 'English'
    }
  ])

  const currentLang = computed(
    () => languages.value.find(l => l.code === locale.value) ?? languages.value[0]
  )

  function selectLang(code) {
    locale.value = code
    menuOpen.value = false
  }

  return { menuOpen, languages, currentLang, selectLang, locale }
}
