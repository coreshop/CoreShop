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
import { StateConditionBase } from '../StateConditionBase'
import { WorkflowNames } from '../../api/workflow-api'

export const PaymentStateCondition: React.FC<ConditionComponentProps> = (props) => {
  const { t } = useTranslation()

  return (
    <StateConditionBase
      {...props}
      workflowName={WorkflowNames.PAYMENT}
      fieldName="paymentState"
      label={t('coreshop_payment_state', { defaultValue: 'Payment State' })}
    />
  )
}
