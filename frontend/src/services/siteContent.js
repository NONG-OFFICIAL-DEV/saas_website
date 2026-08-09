import cmsApi from '@/services/cmsApi'

// The backend splits each block into non-translatable `data` (emails,
// phones, URLs) and translatable `content` (headlines, bios, labels) — see
// SiteContentSeeder.php. These lists say which flat field goes in which
// half when we send an update back.
const DATA_KEYS = {
  hero: ['cta_secondary_url'],
  about: ['email', 'socials'],
  footer: ['email', 'phone', 'socials']
}

function flatten(block) {
  if (!block) return null
  return { id: block.id, ...block.data, ...(block.content ?? {}) }
}

function split(key, payload) {
  const dataKeys = DATA_KEYS[key] ?? []
  const data = {}
  const content = {}
  for (const [k, v] of Object.entries(payload)) {
    if (k === 'id') continue
    if (dataKeys.includes(k)) data[k] = v
    else content[k] = v
  }
  return { data, content }
}

// Public reads are deliberately silent (the store shows fallback copy
// immediately, per stores/siteContent.js) and the admin editor already has
// its own Save-button spinner — skip the global overlay for both so it
// never fights with those.
const NO_OVERLAY = { meta: { loader: 'skip' } }

async function getBlock(key) {
  const { data } = await cmsApi.get(`/public/site-content/${key}`, NO_OVERLAY)
  return flatten(data.data)
}

async function updateBlock(key, payload) {
  const { data, content } = split(key, payload)
  const { data: res } = await cmsApi.put(
    `/admin/site-content/${key}`,
    { locale: 'en', data, content },
    NO_OVERLAY
  )
  return flatten(res.data)
}

export const getHero = () => getBlock('hero')
export const updateHero = (id, payload) => updateBlock('hero', payload)

export const getAbout = () => getBlock('about')
export const updateAbout = (id, payload) => updateBlock('about', payload)

export const getFooter = () => getBlock('footer')
export const updateFooter = (id, payload) => updateBlock('footer', payload)
