import cmsApi from '~/services/cmsApi'

// The backend splits each block into non-translatable `data` (emails,
// phones, URLs) and translatable `content` (headlines, bios, labels) — see
// SiteContentSeeder.php. These lists say which flat field goes in which
// half when we send an update back.
const DATA_KEYS: Record<string, string[]> = {
  hero: ['cta_secondary_url'],
  about: ['email', 'socials', 'profile_photo_url'],
  footer: ['email', 'phone', 'socials']
}

function flatten(block: any): Record<string, any> | null {
  if (!block) return null
  return { id: block.id, ...block.data, ...(block.content ?? {}) }
}

function split(key: string, payload: Record<string, any>) {
  const dataKeys = DATA_KEYS[key] ?? []
  const data: Record<string, any> = {}
  const content: Record<string, any> = {}
  for (const [k, v] of Object.entries(payload)) {
    if (k === 'id') continue
    if (dataKeys.includes(k)) data[k] = v
    else content[k] = v
  }
  return { data, content }
}

// Public reads are deliberately silent (the store shows fallback copy
// immediately, per stores/siteContent.ts) and the admin editor already has
// its own Save-button spinner — skip the global overlay for both so it
// never fights with those.
const NO_OVERLAY = { meta: { loader: 'skip' as const } }

async function getBlock(key: string) {
  const { data } = await cmsApi.get(`/public/site-content/${key}`, NO_OVERLAY)
  return flatten(data.data)
}

async function updateBlock(key: string, payload: Record<string, any>) {
  const { data, content } = split(key, payload)
  const { data: res } = await cmsApi.put(
    `/admin/site-content/${key}`,
    { locale: 'en', data, content },
    NO_OVERLAY
  )
  return flatten(res.data)
}

export const getHero = () => getBlock('hero')
export const updateHero = (id: string | undefined, payload: Record<string, any>) => updateBlock('hero', payload)

export const getAbout = () => getBlock('about')
export const updateAbout = (id: string | undefined, payload: Record<string, any>) => updateBlock('about', payload)

export const getFooter = () => getBlock('footer')
export const updateFooter = (id: string | undefined, payload: Record<string, any>) => updateBlock('footer', payload)
