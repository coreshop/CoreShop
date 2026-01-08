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
import { TransitionConditionBase } from '../TransitionConditionBase'
import { WorkflowNames } from '../../api/workflow-api'

export const PaymentTransitionCondition: React.FC<ConditionComponentProps> = (props) => {
  const { t } = useTranslation()

  return (
    <TransitionConditionBase
      {...props}
      workflowName={WorkflowNames.PAYMENT}
      label={t('coreshop_payment_transition', { defaultValue: 'Payment Transition' })}
    />
  )
}
