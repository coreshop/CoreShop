/**
 * CoreShop Order By Number Button Component
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
import {Icon, useFormModal} from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { useDataObjectHelper } from '@pimcore/studio-ui-bundle/modules/data-object'
import { orderService } from '../services/OrderService'
import { message } from 'antd'
import { type MenuButtonProps } from '@coreshop/menu/src'

export const OrderByNumberButton = ({ icon, label }: MenuButtonProps): React.JSX.Element => {
  const { input } = useFormModal()
  const { openDataObject } = useDataObjectHelper()
  const { t } = useTranslation()

  const handleClick = (): void => {
    input({
      title: t('coreshop_order_by_number'),
      label: t('coreshop_please_enter_the_number_of_the_order'),
      rule: {
        required: true,
        message: t('coreshop_please_enter_the_number_of_the_order')
      },
      okText: t('search'),
      cancelText: t('cancel'),
      onOk: async (value: string) => {
        try {
          const result = await orderService.findOrder(value.trim())
          
          if (result.success && result.id) {
            await openDataObject({
              config: {
                id: result.id
              }
            })
          } else {
            message.error(t('element_not_found'))
          }
        } catch (error) {
          console.error('Error searching for order:', error)
          message.error(t('error'))
        }
      }
    })
  }

  return (
    <button
      className="main-nav__list-btn"
      onClick={handleClick}
    >
      <Icon value={icon} />
      {label || t('coreshop_order_by_number')}
    </button>
  )
}