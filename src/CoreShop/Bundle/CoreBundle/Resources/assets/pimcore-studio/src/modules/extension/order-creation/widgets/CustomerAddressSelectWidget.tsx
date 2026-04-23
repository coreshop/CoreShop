/**
 * CoreShop CoreBundle - Customer Address Select Widget
 *
 * Schema-driven widget for address selection in order creation.
 * Reads address options from OrderCreation context instead of schema choices
 * (since no customer is available at schema generation time).
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
import { Select } from 'antd'
import type { AddressInfo } from '@coreshop/order/src/modules/order-creation/types'

const formatAddress = (addr: AddressInfo): string => {
  const parts: string[] = []

  if (addr.firstname || addr.lastname) {
    parts.push([addr.firstname, addr.lastname].filter(Boolean).join(' '))
  }
  if (addr.company) {
    parts.push(addr.company)
  }
  if (addr.street) {
    parts.push([addr.street, addr.number].filter(Boolean).join(' '))
  }
  if (addr.postcode || addr.city) {
    parts.push([addr.postcode, addr.city].filter(Boolean).join(' '))
  }
  if (addr.countryName) {
    parts.push(addr.countryName)
  }

  return parts.join(', ') || `Address #${addr.id}`
}

interface CustomerAddressSelectWidgetProps {
  value?: number | null
  onChange?: (value: number | null) => void
  disabled?: boolean
  addresses?: AddressInfo[]
}

export const CustomerAddressSelectWidget: React.FC<CustomerAddressSelectWidgetProps> = ({
  value,
  onChange,
  disabled,
  addresses = [],
}) => {
  const options = addresses.map((addr) => ({
    value: addr.id,
    label: formatAddress(addr),
  }))

  return (
    <Select
      value={value ?? undefined}
      onChange={(v) => onChange?.(v ?? null)}
      options={options}
      allowClear
      showSearch
      filterOption={(input, option) =>
        (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
      }
      disabled={disabled || addresses.length === 0}
    />
  )
}
