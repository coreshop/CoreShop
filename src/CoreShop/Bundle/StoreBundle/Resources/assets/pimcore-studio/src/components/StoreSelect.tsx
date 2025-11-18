/**
 * CoreShop StoreBundle Studio Plugin
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
import { storeApi, type StoreDetail } from '../modules/stores/api'
import { DroppableEntity } from '@coreshop/resource/src/entities/components/dnd/DroppableEntity'

export const StoreSelect: React.FC<SelectProps> = (props) => {
  const [options, setOptions] = React.useState<Array<{ value: number, label: string }>>([])
  const [loading, setLoading] = React.useState(false)

  React.useEffect(() => {
    void loadStores()
  }, [])

  const loadStores = async (): Promise<void> => {
    setLoading(true)
    try {
      const stores = await storeApi.list()
      setOptions(stores.map(store => ({
        value: store.id,
        label: store.name
      })))
    } catch (err) {
      console.error('Failed to load stores:', err)
    } finally {
      setLoading(false)
    }
  }

  return (
    <DroppableEntity
      accept="coreshop:store"
      isValidData={(info) => typeof info?.data?.id === 'number'}
      onDrop={(info) => {
        if (props.onChange && info?.data?.id) {
          const event = { target: { value: info.data.id } } as any
          props.onChange(info.data.id, event)
        }
      }}
    >
      <Select
        {...props}
        loading={loading}
        options={options}
      />
    </DroppableEntity>
  )
}
