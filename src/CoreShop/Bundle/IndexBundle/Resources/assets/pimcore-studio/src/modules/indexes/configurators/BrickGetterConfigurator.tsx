/**
 * CoreShop IndexBundle Brick Getter Configurator
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

export const BrickGetterConfigurator: React.FC<ConfigComponentProps> = ({ value, onChange }) => {
  return (
    <Form.Item label="Brick Field">
      <Input
        value={value?.brickField}
        onChange={(e) => onChange({ ...value, brickField: e.target.value })}
        placeholder="Enter brick field name"
      />
    </Form.Item>
  )
}
