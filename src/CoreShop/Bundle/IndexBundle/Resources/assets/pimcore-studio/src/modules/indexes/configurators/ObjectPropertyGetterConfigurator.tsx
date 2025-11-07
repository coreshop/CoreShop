/**
 * CoreShop IndexBundle Object Property Getter Configurator
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
import type { ConfigComponentProps } from '../registry'

export const ObjectPropertyGetterConfigurator: React.FC<ConfigComponentProps> = ({ value, onChange }) => {
  const propertyOptions = [
    { value: 'id', label: 'ID' },
    { value: 'key', label: 'Key' },
    { value: 'path', label: 'Path' },
    { value: 'fullPath', label: 'Full Path' },
    { value: 'published', label: 'Published' },
    { value: 'creationDate', label: 'Creation Date' },
    { value: 'modificationDate', label: 'Modification Date' }
  ]

  return (
    <Form.Item label="Property">
      <Select
        value={value?.property}
        onChange={(newValue) => onChange({ ...value, property: newValue })}
        options={propertyOptions}
        placeholder="Select property"
        showSearch
        filterOption={(input, option) =>
          (option?.label ?? '').toLowerCase().includes(input.toLowerCase())
        }
      />
    </Form.Item>
  )
}
