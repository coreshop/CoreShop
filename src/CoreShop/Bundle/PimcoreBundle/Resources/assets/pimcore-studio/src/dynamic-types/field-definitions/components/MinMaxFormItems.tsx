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
import { Form, InputNumber } from '@pimcore/studio-ui-bundle/components'

interface MinMaxFormItemsProps {
  /** Number of decimal places; CoreShop money values are stored as integers (cents). */
  precision?: number
}

export const MinMaxFormItems = ({ precision }: MinMaxFormItemsProps): React.JSX.Element => {
  const { t } = useTranslation()

  return (
    <>
      <Form.Item
        label={t('min-value')}
        name="minValue"
      >
        <InputNumber precision={precision} />
      </Form.Item>

      <Form.Item
        label={t('max-value')}
        name="maxValue"
      >
        <InputNumber precision={precision} />
      </Form.Item>
    </>
  )
}
