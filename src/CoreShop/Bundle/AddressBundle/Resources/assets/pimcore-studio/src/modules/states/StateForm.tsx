/**
 * CoreShop AddressBundle - State Form (Form Builder Version)
 *
 * Form component using the new FormBuilder pattern.
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
import { container } from '@pimcore/studio-ui-bundle'
import { DynamicForm, type FormBuilder } from '@coreshop/resource/src/entities/form-builder'
import type { StateDetail } from './api'
import { Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'

export interface StateFormProps {
  data?: StateDetail
  onChange: (draft: Partial<StateDetail>) => void
  currentLocale: string
  locales?: string[]
}

/**
 * State Form Component
 *
 * Uses FormBuilder pattern for composable, extensible form configuration.
 * Base form is defined in AddressBundle, extensions can be added by other bundles.
 */
export const StateForm: React.FC<StateFormProps> = ({
  data,
  onChange,
  currentLocale,
  locales
}) => {
  const { t } = useTranslation()

  // Get the form builder from container
  const builder = container.get<FormBuilder<StateDetail>>(
    'CoreShop/Address/State/FormBuilder'
  )

  // Build final config with all decorators applied
  const config = React.useMemo(() => {
    return builder.build({
      data,
      locale: currentLocale,
      locales
    })
  }, [builder, data, currentLocale, locales])

  return (
    <div style={{ padding: 12 }}>
      <Space
        align="baseline"
        style={{
          display: 'flex',
          justifyContent: 'space-between',
          marginBottom: 16
        }}
      >
        <Typography.Title level={5} style={{ margin: 0 }}>
          {t('coreshop_state_configuration', { defaultValue: 'State Configuration' })}
        </Typography.Title>
        <Typography.Text type="secondary">
          {currentLocale.toUpperCase()}
        </Typography.Text>
      </Space>

      <DynamicForm
        config={config}
        data={data}
        onChange={onChange}
        currentLocale={currentLocale}
      />
    </div>
  )
}
