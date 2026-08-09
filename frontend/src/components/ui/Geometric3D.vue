<template>
  <svg
    class="geo3d"
    viewBox="0 0 480 480"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    :aria-hidden="true"
  >
    <defs>
      <linearGradient id="geoTop" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" :stop-color="shades.light" />
        <stop offset="100%" :stop-color="shades.mid" />
      </linearGradient>
      <linearGradient id="geoLeft" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" :stop-color="shades.mid" />
        <stop offset="100%" :stop-color="shades.dark" />
      </linearGradient>
      <linearGradient id="geoRight" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" :stop-color="shades.midLight" />
        <stop offset="100%" :stop-color="shades.mid" />
      </linearGradient>
      <radialGradient id="geoSphere" cx="35%" cy="30%" r="70%">
        <stop offset="0%" :stop-color="shades.light" />
        <stop offset="100%" :stop-color="shades.dark" />
      </radialGradient>
    </defs>

    <!-- Floating ring -->
    <g class="geo-float geo-float--slow">
      <ellipse
        cx="110"
        cy="330"
        rx="62"
        ry="22"
        :stroke="shades.mid"
        stroke-width="10"
        opacity="0.55"
        transform="rotate(-18 110 330)"
      />
    </g>

    <!-- Isometric cube stack -->
    <g class="geo-float geo-float--med">
      <!-- shadow -->
      <ellipse cx="260" cy="360" rx="120" ry="18" fill="#000" opacity="0.12" />
      <!-- top face -->
      <polygon points="260,150 360,205 260,260 160,205" fill="url(#geoTop)" />
      <!-- left face -->
      <polygon points="160,205 260,260 260,340 160,285" fill="url(#geoLeft)" />
      <!-- right face -->
      <polygon points="260,260 360,205 360,285 260,340" fill="url(#geoRight)" />
    </g>

    <!-- Floating sphere -->
    <g class="geo-float geo-float--fast">
      <ellipse cx="352" cy="150" rx="34" ry="10" fill="#000" opacity="0.1" />
      <circle cx="352" cy="118" r="38" fill="url(#geoSphere)" />
    </g>

    <!-- Small particles -->
    <g class="geo-float geo-float--fast" opacity="0.7">
      <circle cx="120" cy="120" r="7" :fill="shades.mid" />
      <circle cx="90" cy="160" r="4" :fill="shades.light" />
    </g>
    <g class="geo-float geo-float--slow" opacity="0.6">
      <circle cx="400" cy="300" r="6" :fill="shades.midLight" />
    </g>
  </svg>
</template>

<script setup>
  import { computed } from 'vue'

  const props = defineProps({
    accent: {
      type: String,
      default: '#6366F1'
    }
  })

  function hexToHsl(hex) {
    const clean = hex.replace('#', '')
    const r = parseInt(clean.substring(0, 2), 16) / 255
    const g = parseInt(clean.substring(2, 4), 16) / 255
    const b = parseInt(clean.substring(4, 6), 16) / 255
    const max = Math.max(r, g, b)
    const min = Math.min(r, g, b)
    let h = 0
    let s = 0
    const l = (max + min) / 2
    const d = max - min
    if (d !== 0) {
      s = d / (1 - Math.abs(2 * l - 1))
      switch (max) {
        case r: h = ((g - b) / d) % 6; break
        case g: h = (b - r) / d + 2; break
        default: h = (r - g) / d + 4
      }
      h *= 60
      if (h < 0) h += 360
    }
    return { h, s, l }
  }

  function hslToHex(h, s, l) {
    const c = (1 - Math.abs(2 * l - 1)) * s
    const x = c * (1 - Math.abs(((h / 60) % 2) - 1))
    const m = l - c / 2
    let r = 0, g = 0, b = 0
    if (h < 60) [r, g, b] = [c, x, 0]
    else if (h < 120) [r, g, b] = [x, c, 0]
    else if (h < 180) [r, g, b] = [0, c, x]
    else if (h < 240) [r, g, b] = [0, x, c]
    else if (h < 300) [r, g, b] = [x, 0, c]
    else [r, g, b] = [c, 0, x]
    const toHex = v =>
      Math.round((v + m) * 255)
        .toString(16)
        .padStart(2, '0')
    return `#${toHex(r)}${toHex(g)}${toHex(b)}`
  }

  function adjustLightness(hex, amount) {
    const { h, s, l } = hexToHsl(hex)
    const newL = Math.min(1, Math.max(0, l + amount))
    return hslToHex(h, s, newL)
  }

  const shades = computed(() => ({
    light: adjustLightness(props.accent, 0.22),
    midLight: adjustLightness(props.accent, 0.1),
    mid: props.accent,
    dark: adjustLightness(props.accent, -0.18)
  }))
</script>

<style scoped>
  .geo3d {
    width: 100%;
    height: 100%;
    overflow: visible;
  }

  .geo-float {
    transform-box: fill-box;
    transform-origin: center;
  }
  .geo-float--slow {
    animation: geo-bob 7s ease-in-out infinite;
  }
  .geo-float--med {
    animation: geo-bob 5.5s ease-in-out infinite;
    animation-delay: 0.4s;
  }
  .geo-float--fast {
    animation: geo-bob 4s ease-in-out infinite;
    animation-delay: 0.8s;
  }

  @keyframes geo-bob {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-14px); }
  }

  @media (prefers-reduced-motion: reduce) {
    .geo-float {
      animation: none;
    }
  }
</style>
