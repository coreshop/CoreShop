/**
 * CoreShop ProductBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { Input, Typography } from 'antd'
import {
  DynamicTypeObjectDataAbstract
} from '@pimcore/studio-ui-bundle/modules/element'

const { Text } = Typography

interface UnitDefinitionValue {
  id?: number
  unitName?: string
  conversationRate?: number
  precision?: number
  unit?: {
    id?: number
    name?: string
  }
}

interface UnitDefinitionInnerProps {
  value?: UnitDefinitionValue
  disabled?: boolean
  style?: React.CSSProperties
}

const UnitDefinitionInner: React.FC<UnitDefinitionInnerProps> = ({
  value,
  disabled,
  style
}) => {
  if (!value || !value.id) {
    return (
      <Input
        value=""
        disabled
        style={style}
        placeholder="No unit definition selected"
      />
    )
  }

  const displayValue = value.unitName
    ? `${value.unitName} (${value.conversationRate ?? 1})`
    : `${value.unit?.name ?? 'Unit'} (${value.conversationRate ?? 1})`

  return (
    <Input
      value={displayValue}
      disabled={disabled}
      readOnly
      style={style}
    />
  )
}

export class DynamicTypeObjectDataCoreShopProductUnitDefinition extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopProductUnitDefinition'

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, ...rest } = props

    return (
      <UnitDefinitionInner
        value={rest.value}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
      />
    )
  }

  getVersionObjectDataComponent(props: any): React.ReactElement {
    return this.getObjectDataComponent({ ...props, noteditable: true })
  }
}
