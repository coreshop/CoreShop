/**
 * CoreShop IndexBundle Index Settings Form
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
import { Form, Input, Select, Checkbox, Card } from 'antd'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import type { Index, IndexConfig } from '../api'
import type { WorkerConfiguratorRegistry } from '../registry'
import { serviceIds } from '../service-ids'

interface SettingsFormProps {
  index: Index
  config: IndexConfig
  onChange: (index: Index) => void
}

export const SettingsForm: React.FC<SettingsFormProps> = ({ index, config, onChange }) => {
  const { t } = useTranslation()
  // Get worker configurator registry - memoized to prevent re-fetching on every render
  const workerConfiguratorRegistry = React.useMemo(
    () => container.get<WorkerConfiguratorRegistry>(serviceIds.workerConfiguratorRegistry),
    []
  )

  const handleFieldChange = (field: keyof Index, value: any) => {
    onChange({ ...index, [field]: value })
  }

  const handleConfigurationChange = (configuration: Record<string, any>) => {
    onChange({
      ...index,
      configuration
    })
  }

  // Get worker configurator component
  const WorkerConfiguratorComponent = React.useMemo(() => {
    if (!index.worker) return null
    const result = workerConfiguratorRegistry.get(index.worker)

    // Handle both direct component and wrapped {type, component} format
    if (result && typeof result === 'object' && 'component' in result) {
      return (result as any).component
    }
    return result
  }, [index.worker])

  // Get worker types - handle both array and object formats
  let workerTypes: string[] = []
  if (Array.isArray(config.workerTypes)) {
    workerTypes = config.workerTypes
  } else if (config.workerTypes && typeof config.workerTypes === 'object') {
    workerTypes = Object.keys(config.workerTypes)
  } else if (Array.isArray(config.workers)) {
    workerTypes = config.workers
  }

  const workerOptions = workerTypes.map(worker => ({
    label: worker,
    value: worker
  }))

  // Get class options
  const classOptions = config.classes.map(cls => ({
    label: cls.name,
    value: cls.name
  }))

  return (
    <Form layout="vertical">
      <Form.Item
        label={t('coreshop_name', { defaultValue: 'Name' })}
        required
      >
        <Input
          value={index.name}
          onChange={(e) => handleFieldChange('name', e.target.value)}
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_class', { defaultValue: 'Class' })}
        required
      >
        <Select
          value={index.class}
          onChange={(value) => handleFieldChange('class', value)}
          options={classOptions}
          showSearch
          filterOption={(input, option) =>
            (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
          }
        />
      </Form.Item>

      <Form.Item
        label={t('coreshop_indexes_worker', { defaultValue: 'Worker' })}
        required
      >
        <Select
          value={index.worker}
          onChange={(value) => handleFieldChange('worker', value)}
          options={workerOptions}
        />
      </Form.Item>

      <Form.Item>
        <Checkbox
          checked={index.indexLastVersion ?? false}
          onChange={(e) => handleFieldChange('indexLastVersion', e.target.checked)}
        >
          {t('coreshop_indexes_index_last_version', { defaultValue: 'Index Last Version (unchecked = published version)' })}
        </Checkbox>
      </Form.Item>

      {/* Worker-specific configuration */}
      {index.worker && WorkerConfiguratorComponent && (
        <Card
          title={t('coreshop_indexes_worker_configuration', { defaultValue: `${index.worker.toUpperCase()} Configuration` })}
          style={{ marginTop: 16 }}
          size="small"
        >
          <WorkerConfiguratorComponent
            configuration={index.configuration || {}}
            onChange={handleConfigurationChange}
          />
        </Card>
      )}

      {index.worker && !WorkerConfiguratorComponent && (
        <div style={{
          padding: 12,
          background: 'var(--ant-color-warning-bg)',
          border: '1px solid var(--ant-color-warning-border)',
          borderRadius: 4,
          color: 'var(--ant-color-warning-text)'
        }}>
          {t('coreshop_indexes_no_configurator', { defaultValue: `No configurator available for worker type "${index.worker}"` })}
        </div>
      )}
    </Form>
  )
}
