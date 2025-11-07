/**
 * CoreShop IndexBundle Expression Interpreter Configurator
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

export const ExpressionInterpreterConfigurator: React.FC<ConfigComponentProps> = ({ value, onChange }) => {
  return (
    <Form.Item label="Expression">
      <Input.TextArea
        value={value?.expression}
        onChange={(e) => onChange({ ...value, expression: e.target.value })}
        placeholder="Enter expression (e.g., value * 100)"
        rows={3}
      />
    </Form.Item>
  )
}
