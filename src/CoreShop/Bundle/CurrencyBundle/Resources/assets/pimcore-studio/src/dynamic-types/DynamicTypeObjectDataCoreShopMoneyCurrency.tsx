/**
 * CoreShop CurrencyBundle Studio Plugin
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React from 'react'
import { InputNumber, Select, Space } from 'antd'
import {
  DynamicTypeObjectDataAbstract,
  DynamicTypeFieldFilterNumber
} from '@pimcore/studio-ui-bundle/modules/element'
import { loadCurrencies, type Option } from './DynamicTypeObjectDataCoreShopCurrency'

interface MoneyCurrencyValue {
  value?: number | null
  currency?: number | null
}

interface MoneyCurrencyInnerProps {
  value?: MoneyCurrencyValue
  onChange?: (value: MoneyCurrencyValue) => void
  disabled?: boolean
  style?: React.CSSProperties
  minValue?: number | null
  maxValue?: number | null
  decimalPrecision?: number
}

const MoneyCurrencyInner: React.FC<MoneyCurrencyInnerProps> = ({
  value,
  onChange,
  disabled,
  style,
  minValue,
  maxValue,
  decimalPrecision = 2
}) => {
  const [options, setOptions] = React.useState<Option[]>([])
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    void (async () => {
      try {
        const opts = await loadCurrencies()
        setOptions(opts)
      } finally {
        setLoading(false)
      }
    })()
  }, [])

  const handleValueChange = (newValue: number | null) => {
    onChange?.({
      ...value,
      value: newValue
    })
  }

  const handleCurrencyChange = (newCurrency: number) => {
    onChange?.({
      ...value,
      currency: newCurrency
    })
  }

  return (
    <Space.Compact style={style}>
      <InputNumber
        value={value?.value}
        onChange={handleValueChange}
        disabled={disabled}
        min={minValue ?? undefined}
        max={maxValue ?? undefined}
        precision={decimalPrecision}
        style={{ flex: 1, minWidth: 120 }}
      />
      <Select
        value={value?.currency}
        onChange={handleCurrencyChange}
        options={options}
        loading={loading}
        disabled={disabled}
        showSearch
        optionFilterProp="label"
        allowClear
        style={{ minWidth: 100 }}
        placeholder="Currency"
      />
    </Space.Compact>
  )
}

export class DynamicTypeObjectDataCoreShopMoneyCurrency extends DynamicTypeObjectDataAbstract {
  readonly id = 'coreShopMoneyCurrency'
  readonly dynamicTypeFieldFilterType = new DynamicTypeFieldFilterNumber()

  getObjectDataComponent(props: any): React.ReactElement {
    const { name, noteditable, defaultFieldWidth, minValue, maxValue, decimalPrecision, ...rest } = props

    return (
      <MoneyCurrencyInner
        value={rest.value}
        onChange={rest.onChange}
        disabled={noteditable === true}
        style={{ width: defaultFieldWidth?.width ?? '100%' }}
        minValue={minValue}
        maxValue={maxValue}
        decimalPrecision={decimalPrecision ?? 2}
      />
    )
  }

  getVersionObjectDataComponent(props: any): React.ReactElement {
    return this.getObjectDataComponent({ ...props, noteditable: true })
  }
}
