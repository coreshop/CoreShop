/**
 * CoreShop OrderBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Tag } from 'antd'
import { isNil } from 'lodash'
import { type DefaultCellProps } from '@pimcore/studio-ui-bundle/components'

interface OrderStateValue {
  label: string
  color: string | null
}

export const OrderStateCell = (props: DefaultCellProps): React.JSX.Element => {
  const value = props.getValue() as OrderStateValue | null

  if (isNil(value) || isNil(value.label)) {
    return <div className='default-cell__content' />
  }

  const { label, color } = value

  if (isNil(color)) {
    return (
      <div className='default-cell__content default-cell__content--padded'>
        {label}
      </div>
    )
  }

  return (
    <div className='default-cell__content default-cell__content--padded'>
      <Tag color={color}>
        {label}
      </Tag>
    </div>
  )
}
