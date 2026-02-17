/**
 * CoreShop ResourceBundle - Error Handling Utilities
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { trackError, GeneralError } from '@pimcore/studio-ui-bundle/modules/app'

/**
 * Extract a user-friendly error message from various error types
 */
export const getErrorMessage = (error: unknown, fallback = 'An error occurred'): string => {
  if (error instanceof Error) {
    return error.message
  }
  if (typeof error === 'string') {
    return error
  }
  if (error && typeof error === 'object') {
    // Handle API error responses
    const errObj = error as Record<string, unknown>
    if (typeof errObj.message === 'string') return errObj.message
    if (typeof errObj.detail === 'string') return errObj.detail
    if (typeof errObj.error === 'string') return errObj.error
  }
  return fallback
}

/**
 * Track an error using Pimcore Studio's error tracking system.
 * This displays an error modal to the user.
 * Use this for critical errors that need user attention.
 */
export const handleCriticalError = (error: unknown, context?: string): void => {
  const message = getErrorMessage(error, context ?? 'A critical error occurred')
  console.error(context ?? 'Critical error:', error)
  trackError(new GeneralError(message))
}

/**
 * Log an error to console without showing to user.
 * Use this for non-critical errors that shouldn't interrupt the user.
 */
export const logError = (error: unknown, context?: string): void => {
  const message = getErrorMessage(error)
  console.error(context ?? 'Error:', message, error)
}

/**
 * Renders an API error message for use in messageApi.error().
 * Supports multi-line messages (newline-separated from backend)
 * by rendering each line as a separate <div>.
 */
export const renderApiError = (message: unknown): React.ReactNode => {
  if (typeof message === 'string' && message.includes('\n')) {
    return React.createElement(
      'div',
      null,
      ...message.split('\n').map((line, i) =>
        React.createElement('div', { key: i }, line)
      )
    )
  }
  return message as React.ReactNode
}
