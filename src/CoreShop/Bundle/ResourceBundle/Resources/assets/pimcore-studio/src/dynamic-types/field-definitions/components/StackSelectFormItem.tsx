/**
 * CoreShop ResourceBundle Studio Plugin
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { useEffect, useState } from 'react'
import { useTranslation } from 'react-i18next'
import { container } from '@pimcore/studio-ui-bundle'
import { Form, Select } from '@pimcore/studio-ui-bundle/components'
import { coreshopResourceServiceIds, type ResourceConfigProvider } from '../../../config'

let cachedStackOptions: Array<{ label: string, value: string }> | null = null

/**
 * Select for the CoreShop resource stack (e.g. "coreshop.product") used by
 * coreShopRelation / coreShopRelations. Options come from the resource config endpoint.
 */
export const StackSelectFormItem = (): React.JSX.Element => {
  const { t } = useTranslation()
  const [options, setOptions] = useState(cachedStackOptions ?? [])

  useEffect(() => {
    if (cachedStackOptions !== null) {
      return
    }

    let cancelled = false

    const load = async (): Promise<void> => {
      try {
        const provider = container.get<ResourceConfigProvider>(coreshopResourceServiceIds.configProvider)
        const config = await provider.getConfig()
        cachedStackOptions = (config.full_stack ?? []).map(stack => ({ label: stack, value: stack }))

        if (!cancelled) {
          setOptions(cachedStackOptions)
        }
      } catch {
        // Keep the select usable even if the config endpoint is unavailable.
      }
    }

    void load()

    return () => {
      cancelled = true
    }
  }, [])

  return (
    <Form.Item
      label={t('coreshop_field_definition_stack')}
      name="stack"
    >
      <Select
        allowClear
        options={options}
        showSearch
      />
    </Form.Item>
  )
}
