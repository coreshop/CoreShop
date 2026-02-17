/**
 * CoreShop RuleBundle Studio Plugin
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
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { EntitySplitManager, getErrorMessage, renderApiError } from '@coreshop/resource'
import type { Rule, RuleConfig } from '../types'
import type { RuleApi } from '../api'

interface RuleManagerProps<T extends Rule> {
  api: RuleApi<T>
  renderForm: (rule: T, config: RuleConfig, onSave: (rule: T) => Promise<void>, onChange: (rule: T) => void) => React.ReactNode
  createEmptyRule: () => T
}

export function RuleManager<T extends Rule>({
  api,
  renderForm,
  createEmptyRule
}: RuleManagerProps<T>) {
  const messageApi = useMessage()
  const [config, setConfig] = React.useState<RuleConfig>({ conditions: [], actions: [] })

  // Load config
  React.useEffect(() => {
    api.getConfig()
      .then(setConfig)
      .catch(err => {
        void messageApi.error(renderApiError(getErrorMessage(err, 'Failed to load configuration')))
      })
  }, [api])

  return (
    <EntitySplitManager
      api={api}
      createEmpty={createEmptyRule}
      leftRootTitle="Rules"
      renderDetail={(data, loading, onSave, onChange) =>
        data ? renderForm(data, config, onSave, onChange) : null
      }
    />
  )
}
