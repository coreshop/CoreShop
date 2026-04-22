/**
 * CoreShop CoreBundle Studio Plugin
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
import dayjs from 'dayjs'
import { ReportPanel } from './ReportPanel'
import type { ReportConfig } from '../types'

const abandonedCartsConfig: ReportConfig = {
  type: 'carts_abandoned',
  name: 'coreshop_report_carts_abandoned',
  icon: 'StopOutlined',
  hasStoreFilter: true,
  hasPagination: true
}

/**
 * AbandonedCartsReport - Table showing abandoned carts
 */
export const AbandonedCartsReport: React.FC = () => {
  const { t } = useTranslation()

  const columns = [
    {
      title: t('coreshop_report_user_name', { defaultValue: 'User' }),
      dataIndex: 'userName',
      key: 'userName'
    },
    {
      title: t('coreshop_report_user_email', { defaultValue: 'Email' }),
      dataIndex: 'email',
      key: 'email'
    },
    {
      title: t('coreshop_report_selected_payment', { defaultValue: 'Payment' }),
      dataIndex: 'selectedPayment',
      key: 'selectedPayment'
    },
    {
      title: t('coreshop_report_creation_date', { defaultValue: 'Created' }),
      dataIndex: 'creationDate',
      key: 'creationDate',
      render: (val: number) => val ? dayjs.unix(val).format('YYYY-MM-DD HH:mm') : ''
    },
    {
      title: t('coreshop_report_modifiction_date', { defaultValue: 'Modified' }),
      dataIndex: 'modificationDate',
      key: 'modificationDate',
      render: (val: number) => val ? dayjs.unix(val).format('YYYY-MM-DD HH:mm') : ''
    },
    {
      title: t('coreshop_report_items_in_cart', { defaultValue: 'Items' }),
      dataIndex: 'itemsInCart',
      key: 'itemsInCart',
      align: 'right' as const
    }
  ]

  return (
    <ReportPanel
      config={abandonedCartsConfig}
      columns={columns}
    />
  )
}
