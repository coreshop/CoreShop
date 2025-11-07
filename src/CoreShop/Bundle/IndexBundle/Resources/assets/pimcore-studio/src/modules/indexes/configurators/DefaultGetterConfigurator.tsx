/**
 * CoreShop IndexBundle Default Getter Configurator
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
import { Input } from 'antd'
import type { GetterConfiguratorProps } from '../registry'

/**
 * Default Getter Configurator - Simple JSON editor
 */
export const DefaultGetterConfigurator: React.FC<GetterConfiguratorProps> = ({ config, onChange }) => {
  const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    try {
      const parsed = JSON.parse(e.target.value)
      onChange(parsed)
    } catch (err) {
      // Invalid JSON, don't update
    }
  }

  return (
    <div>
      <p style={{ marginBottom: 8, color: 'var(--ant-color-text-secondary)' }}>
        Configure getter parameters as JSON
      </p>
      <Input.TextArea
        rows={4}
        defaultValue={JSON.stringify(config, null, 2)}
        onChange={handleChange}
        placeholder='{ "key": "value" }'
      />
    </div>
  )
}
