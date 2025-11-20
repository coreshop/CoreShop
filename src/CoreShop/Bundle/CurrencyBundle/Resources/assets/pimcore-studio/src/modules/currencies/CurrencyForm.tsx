/**
 * CoreShop CurrencyBundle - Currency Form (Form Builder Version)
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
import type { CurrencyDetail } from './api'
import { Space, Typography } from 'antd'
import { useTranslation } from 'react-i18next'

export interface CurrencyFormProps {
  data?: CurrencyDetail
  onChange: (draft: Partial<CurrencyDetail>) => void
  currentLocale?: string
  locales?: string[]
}

/**
 * Currency Form Component
 *
 * Uses FormBuilder pattern for composable, extensible form configuration.
 * Base form is defined in CurrencyBundle, extensions can be added by other bundles.
 */
export const CurrencyForm: React.FC<CurrencyFormProps> = ({
  data,
  onChange,
  currentLocale,
  locales
}) => {
  const { t } = useTranslation()

  // Get the form builder from container
  const builder = container.get<FormBuilder<CurrencyDetail>>(
    'CoreShop/Currency/Currency/FormBuilder'
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
          {t('coreshop_currency_configuration', { defaultValue: 'Currency Configuration' })}
        </Typography.Title>
        {currentLocale && (
          <Typography.Text type="secondary">
            {currentLocale.toUpperCase()}
          </Typography.Text>
        )}
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
