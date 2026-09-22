/**
 * CoreShop PimcoreBundle Studio Plugin
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
import { useTranslation } from 'react-i18next'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'

export const WidthFormItem = (): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <Form.Item
      label={t('width')}
      name="width"
      tooltip={t('width-tooltip')}
    >
      <Input />
    </Form.Item>
  )
}
