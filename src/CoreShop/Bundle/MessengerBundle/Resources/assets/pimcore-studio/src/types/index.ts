/**
 * CoreShop MessengerBundle Types
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface MessengerChartData {
  receiver: string
  count: number
}

export interface MessengerMessage {
  id: string
  class: string
  serialized?: string
}

export interface MessengerFailedMessage extends MessengerMessage {
  failed_at: string
  failedAt: string
  error: string
}

export interface MessengerReceiver {
  receiver: string
}

export interface MessengerApiResponse<T> {
  success: boolean
  data: T[]
  total?: number
  message?: string
}

export interface MessengerCountResponse {
  success: boolean
  data: MessengerChartData[]
}

export interface MessengerDeleteResponse {
  success: boolean
  message?: string
}

export interface MessengerRetryResponse {
  success: boolean
  message?: string
}