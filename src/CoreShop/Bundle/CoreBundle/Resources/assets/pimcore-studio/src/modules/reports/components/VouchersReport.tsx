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

const vouchersConfig: ReportConfig = {
  type: 'vouchers',
  name: 'coreshop_report_vouchers',
  icon: 'TagOutlined',
  hasStoreFilter: true,
  hasPagination: true
}

/**
 * VouchersReport - Table showing voucher usage
 */
export const VouchersReport: React.FC = () => {
  const { t } = useTranslation()

  const columns = [
    {
      title: t('coreshop_report_voucher_code', { defaultValue: 'Code' }),
      dataIndex: 'code',
      key: 'code'
    },
    {
      title: t('coreshop_report_voucher_discount', { defaultValue: 'Discount' }),
      dataIndex: 'discount',
      key: 'discount'
    },
    {
      title: t('coreshop_report_voucher_pricerule', { defaultValue: 'Price Rule' }),
      dataIndex: 'rule',
      key: 'rule'
    },
    {
      title: t('coreshop_report_voucher_applied_date', { defaultValue: 'Applied Date' }),
      dataIndex: 'usedDate',
      key: 'usedDate',
      render: (val: number) => val ? dayjs.unix(val).format('YYYY-MM-DD HH:mm') : ''
    }
  ]

  return (
    <ReportPanel
      config={vouchersConfig}
      columns={columns}
    />
  )
}
