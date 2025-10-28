/**
 * CoreShop CoreBundle Studio Plugin
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
import { Select, type SelectProps } from 'antd'
// @ts-ignore
import { stateApi } from '@coreshop/address/src/modules/states/api'

interface StateSelectInputProps extends Omit<SelectProps, 'options'> {
  value?: number
  onChange?: (value: number | undefined) => void
  countryId?: number
}

export const StateSelectInput: React.FC<StateSelectInputProps> = ({
  value,
  onChange,
  countryId,
  ...selectProps
}) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    setLoading(true)
    stateApi.list()
      .then((rows: any) => {
        const list = Array.isArray(rows) ? rows : []
        const opts = list
          .map((r: any) => ({
            value: r.id,
            label: r.name ?? String(r.id)
          }))
          .filter((o: any) => o.value != null && o.label)
        setOptions(opts)
      })
      .catch((error) => {
        console.error('StateSelectInput - Error loading states:', error)
        setOptions([])
      })
      .finally(() => setLoading(false))
  }, [])

  return (
    <Select
      {...selectProps}
      value={value}
      onChange={onChange}
      options={options}
      loading={loading}
      showSearch
      optionFilterProp="label"
      placeholder="Select state"
    />
  )
}
