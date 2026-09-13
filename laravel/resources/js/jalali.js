const DAY = 86_400_000

const NUMERIC_LATN = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
  year: 'numeric',
  month: '2-digit',
  day: '2-digit',
  numberingSystem: 'latn',
})

const NUMERIC_FA = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
  year: 'numeric',
  month: '2-digit',
  day: '2-digit',
})

const MONTH_LONG = new Intl.DateTimeFormat('fa-IR-u-ca-persian', { month: 'long' })
const WEEKDAY_SHORT = new Intl.DateTimeFormat('fa-IR-u-ca-persian', { weekday: 'short' })
const DATE_LONG = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
  day: '2-digit',
  month: 'long',
  year: 'numeric',
})
const MONTH_DAY = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
  day: '2-digit',
  month: 'long',
})
const NUMBER_FA = new Intl.NumberFormat('fa-IR', { useGrouping: false })

function parts(date, formatter = NUMERIC_LATN) {
  const result = {}

  for (const part of formatter.formatToParts(date)) {
    if (part.type !== 'literal') result[part.type] = part.value
  }

  return result
}

export const addDays = (date, days) => new Date(date.getTime() + days * DAY)

export function getPersian(date) {
  const p = parts(date)

  return { year: Number(p.year), month: Number(p.month), day: Number(p.day) }
}

export function persianMonthLength(anchor) {
  const probe31 = getPersian(addDays(anchor, 30))
  if (probe31.day === 31) return 31

  const probe30 = getPersian(addDays(anchor, 29))

  return probe30.day === 30 ? 30 : 29
}

export function monthAnchors(anchor) {
  const { month } = getPersian(anchor)
  let first = anchor

  for (let m = month - 1; m >= 1; m--) {
    first = addDays(first, -(m <= 6 ? 31 : 30))
  }

  const anchors = [first]
  let cursor = first

  for (let i = 1; i < 12; i++) {
    cursor = addDays(cursor, persianMonthLength(cursor))
    anchors.push(cursor)
  }

  return anchors
}

function farvardinOfYear(year) {
  const approx = addDays(new Date('1925-03-21T00:00:00Z'), (year - 1304) * 365)

  for (let offset = 0; offset <= 60; offset++) {
    const candidate = addDays(approx, offset)
    const g = getPersian(candidate)

    if (g.year === year && g.month === 1 && g.day === 1) return candidate
    if (g.year > year) break
  }

  return approx
}

export function toGregorian(year, month, day = 1) {
  const farvardin = farvardinOfYear(year)
  let anchor = farvardin

  for (let m = 1; m < month; m++) {
    anchor = addDays(anchor, m <= 6 ? 31 : 30)
  }

  return addDays(anchor, day - 1)
}

export const firstDayOffset = (anchor) => (anchor.getDay() + 1) % 7

export function weekdayLabels() {
  const base = new Date('2023-07-01T00:00:00Z')

  return Array.from({ length: 7 }, (_, i) => Array.from(WEEKDAY_SHORT.format(addDays(base, i)))[0])
}

export const toFaDigits = (value) => NUMBER_FA.format(value)

export const monthName = (date) => MONTH_LONG.format(date)

export const yearLabel = (date) => parts(date, NUMERIC_FA).year

export const dayNumber = (date) => parts(date, NUMERIC_FA).day

export const formatLong = (date) => DATE_LONG.format(date)

export const formatMonthDay = (date) => MONTH_DAY.format(date)

export function formatDate(iso) {
  if (!iso) return null

  const date = new Date(`${iso}T00:00:00`)
  if (Number.isNaN(date.getTime())) return null

  return formatLong(date)
}