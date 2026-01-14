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
import { Icon, useFormModal, useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import { orderService } from '../services/OrderService'
import { type MenuButtonProps } from '@coreshop/menu/src'
import { useWidgetManager } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import { getErrorMessage } from '@coreshop/resource/src/entities'


export const OrderByNumberButton = ({ icon, label }: MenuButtonProps): React.JSX.Element => {
  const { input } = useFormModal()
  const { t } = useTranslation()
  const messageApi = useMessage()
  const widgetManager = useWidgetManager()

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
            widgetManager.openMainWidget({
              name: 'Order #' + result.saleNumber,
              id: 'coreshop-order-detail' + result.id,
              component: 'coreshop-order-detail',
              config: {
                orderId: result.id,
              }
            })
          } else {
            void messageApi.error(t('element_not_found'))
          }
        } catch (error) {
          void messageApi.error(getErrorMessage(error, t('error')))
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