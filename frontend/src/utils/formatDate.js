const UNITS = [
  { limit: 60, divisor: 1, suffix: 's' },
  { limit: 3600, divisor: 60, suffix: 'min' },
  { limit: 86400, divisor: 3600, suffix: 'h' },
  { limit: 2592000, divisor: 86400, suffix: 'd' },
  { limit: 31536000, divisor: 2592000, suffix: 'mes' },
  { limit: Infinity, divisor: 31536000, suffix: 'a' }
]

export function formatRelative(input) {
  if (!input) return ''
  const date = input instanceof Date ? input : new Date(input)
  const seconds = Math.max(0, Math.floor((Date.now() - date.getTime()) / 1000))
  for (const u of UNITS) {
    if (seconds < u.limit) {
      const value = Math.max(1, Math.floor(seconds / u.divisor))
      return `${value}${u.suffix}`
    }
  }
  return ''
}

export function formatDateTime(input) {
  if (!input) return ''
  const date = input instanceof Date ? input : new Date(input)
  return date.toLocaleString('pt-BR')
}
