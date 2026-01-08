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
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'
import { StoreMultiSelect } from '@coreshop/store/src/components/StoreMultiSelect'

interface StoresConditionConfig {
  stores?: number[]
}

/**
 * Stores condition for notification rules - applies to all notification types
 */
export const StoresCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const stores = data?.stores || []

  const handleChange = (value: number[]) => {
    onChange({ ...data, stores: value })
  }

  return (
    <StoreMultiSelect
      name={undefined}
      label={t('coreshop_stores', { defaultValue: 'Stores' })}
      value={stores}
      onChange={handleChange}
    />
  )
}
