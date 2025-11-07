/**
 * CoreShop IndexBundle Classification Store Getter Configurator
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
import { Form, Input } from 'antd'
import type { ConfigComponentProps } from '../registry'

export const ClassificationStoreGetterConfigurator: React.FC<ConfigComponentProps> = ({ value, onChange }) => {
  return (
    <Form.Item label="Classification Store Field">
      <Input
        value={value?.classificationStoreField}
        onChange={(e) => onChange({ ...value, classificationStoreField: e.target.value })}
        placeholder="Enter classification store field name"
      />
    </Form.Item>
  )
}
