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
import { Card } from 'antd'
import { useTranslation } from 'react-i18next'
import { SchemaForm } from '@coreshop/studio-form/src/schema-adapter'
import type { Index, IndexConfig } from '../api'
import { mergeFormDraft } from '../mergeFormDraft'

interface SettingsFormProps {
  index: Index
  config: IndexConfig
  onChange: (index: Index) => void
}

export const SettingsForm: React.FC<SettingsFormProps> = ({ index, config, onChange }) => {
  const { t } = useTranslation()

  const handleConfigurationChange = (configuration: Record<string, any>) => {
    onChange({
      ...index,
      configuration: mergeFormDraft(index.configuration || {}, configuration)
    })
  }

  const workerBlockPrefix = index.worker ? config.workers?.find(w => w.type === index.worker)?.blockPrefix : undefined

  return (
    <>
      <SchemaForm<Index>
        blockPrefix="coreshop_index"
        data={index}
        onChange={(draft) => onChange({ ...index, ...draft })}
      />

      {/* Worker-specific configuration */}
      {index.worker && workerBlockPrefix && (
        <Card
          title={t('coreshop_indexes_worker_configuration', { defaultValue: `${index.worker.toUpperCase()} Configuration` })}
          style={{ marginTop: 16 }}
          size="small"
        >
          <SchemaForm
            blockPrefix={workerBlockPrefix}
            data={index.configuration || {}}
            onChange={handleConfigurationChange}
          />
        </Card>
      )}

      {index.worker && !workerBlockPrefix && (
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
    </>
  )
}
