/**
 * CoreShop PimcoreBundle Date & Currency Utilities
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

/**
 * Format a Unix timestamp or date string to a localized date-time string
 *
 * @param date - Unix timestamp (seconds) or date string
 * @param locale - Locale string (default: 'de-DE')
 * @returns Formatted date-time string or '-' if date is undefined/null
 */
export const formatDateTime = (date?: string | number, locale: string = 'de-DE'): string => {
  if (!date) return '-'

  // Convert to milliseconds if it's a Unix timestamp in seconds
  const dateValue = typeof date === 'number' ? date * 1000 : date

  return new Date(dateValue).toLocaleString(locale, {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

/**
 * Format a Unix timestamp to a localized date string (without time)
 *
 * @param date - Unix timestamp (seconds)
 * @param locale - Locale string (default: 'de-DE')
 * @returns Formatted date string or '-' if date is undefined/null
 */
export const formatDate = (date?: number, locale: string = 'de-DE'): string => {
  if (!date) return '-'

  return new Date(date * 1000).toLocaleDateString(locale, {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

/**
 * Format a currency amount (in cents) to a localized currency string
 *
 * @param amount - Amount in cents (will be divided by 100)
 * @param currencyCode - ISO currency code (e.g., 'EUR', 'USD')
 * @param locale - Locale string (default: 'de-DE')
 * @returns Formatted currency string or '-' if amount is undefined/null
 *
 * @example
 * formatCurrency(12345, 'EUR') // "123,45 €"
 * formatCurrency(undefined, 'EUR') // "-"
 */
export const formatCurrency = (
  amount?: number | null,
  currencyCode: string = 'EUR',
  locale: string = 'de-DE'
): string => {
  if (amount === undefined || amount === null) return '-'

  return new Intl.NumberFormat(locale, {
    style: 'currency',
    currency: currencyCode
  }).format(amount / 100) // Amounts are stored in cents
}

/**
 * Extract currency code from a currency object or string
 *
 * @param currency - Currency object with isoCode property or currency string
 * @returns ISO currency code or 'EUR' as fallback
 *
 * @example
 * getCurrencyCode({ isoCode: 'USD' }) // "USD"
 * getCurrencyCode('GBP') // "GBP"
 * getCurrencyCode(undefined) // "EUR"
 */
export const getCurrencyCode = (currency?: { isoCode?: string } | string): string => {
  if (typeof currency === 'object' && currency?.isoCode) {
    return currency.isoCode
  }
  if (typeof currency === 'string') {
    return currency
  }
  return 'EUR'
}
