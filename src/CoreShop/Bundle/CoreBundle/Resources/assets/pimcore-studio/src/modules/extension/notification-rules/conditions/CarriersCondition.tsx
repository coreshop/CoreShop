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
import { CarrierMultiSelect } from '@coreshop/shipping/src/components/CarrierMultiSelect'

interface CarriersConditionConfig {
  carriers?: number[]
}

/**
 * Carriers condition for notification rules - for order-related notifications
 */
export const CarriersCondition: React.FC<ConditionComponentProps> = ({
  data,
  onChange
}) => {
  const { t } = useTranslation()
  const carriers = data?.carriers || []

  const handleChange = (value: number[]) => {
    onChange({ ...data, carriers: value })
  }

  return (
    <CarrierMultiSelect
      name={undefined}
      label={t('coreshop_carrier', { defaultValue: 'Carriers' })}
      value={carriers}
      onChange={handleChange}
    />
  )
}
