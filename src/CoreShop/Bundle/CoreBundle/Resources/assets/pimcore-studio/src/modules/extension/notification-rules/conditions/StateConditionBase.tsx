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
import { Form, Select, Tag } from 'antd'
import { useTranslation } from 'react-i18next'
import type { ConditionComponentProps } from '@coreshop/rule/src/rules/types'
import { getWorkflowStates, type WorkflowState, type WorkflowName } from '../api/workflow-api'

interface StateConditionConfig {
  [key: string]: string | undefined
}

interface StateConditionBaseProps extends ConditionComponentProps {
  workflowName: WorkflowName
  fieldName: string
  label: string
}

/**
 * Base state condition component that can be used for any workflow
 */
export const StateConditionBase: React.FC<StateConditionBaseProps> = ({
  data,
  onChange,
  workflowName,
  fieldName,
  label
}) => {
  const { t } = useTranslation()
  const [states, setStates] = React.useState<WorkflowState[]>([])
  const [loading, setLoading] = React.useState(true)

  React.useEffect(() => {
    void (async () => {
      setLoading(true)
      try {
        const loadedStates = await getWorkflowStates(workflowName)
        setStates(loadedStates)
      } finally {
        setLoading(false)
      }
    })()
  }, [workflowName])

  const handleChange = (value: string) => {
    onChange({ ...data, [fieldName]: value })
  }

  const options = states.map(state => ({
    value: state.state,
    label: (
      <span>
        <Tag color={state.color} style={{ marginRight: 8 }}>
          {state.state}
        </Tag>
        {state.label}
      </span>
    )
  }))

  return (
    <Form.Item label={label}>
      <Select
        value={data?.[fieldName]}
        onChange={handleChange}
        options={options}
        loading={loading}
        placeholder={t('coreshop_select_state', { defaultValue: 'Select a state' })}
        showSearch
        filterOption={(input, option) =>
          (option?.value as string ?? '').toLowerCase().includes(input.toLowerCase())
        }
      />
    </Form.Item>
  )
}
