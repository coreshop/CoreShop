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
import { Form, Select } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'
import { getWorkflowTransitions, type WorkflowTransition, type WorkflowName } from '../api/workflow-api'

interface TransitionConditionConfig {
  transition?: string
}

interface TransitionConditionBaseProps extends ConditionComponentProps {
  workflowName: WorkflowName
  label: string
}

/**
 * Base transition condition component that can be used for any workflow
 */
export const TransitionConditionBase: React.FC<TransitionConditionBaseProps> = ({
  data,
  onChange,
  workflowName,
  label
}) => {
  const { t } = useTranslation()
  const [transitions, setTransitions] = React.useState<WorkflowTransition[]>([])
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    void (async () => {
      setLoading(true)
      try {
        const loadedTransitions = await getWorkflowTransitions(workflowName)
        setTransitions(loadedTransitions)
      } finally {
        setLoading(false)
      }
    })()
  }, [workflowName])

  const handleChange = (value: string) => {
    onChange({ ...data, transition: value })
  }

  const options = transitions.map(transition => ({
    value: transition.name,
    label: transition.name
  }))

  return (
    <Form.Item label={label}>
      <Select
        value={data?.transition}
        onChange={handleChange}
        options={options}
        loading={loading}
        placeholder={t('coreshop_select_transition', { defaultValue: 'Select a transition' })}
        showSearch
        filterOption={(input, option) =>
          (option?.value as string ?? '').toLowerCase().includes(input.toLowerCase())
        }
      />
    </Form.Item>
  )
}
