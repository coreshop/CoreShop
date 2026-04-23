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
import { Card, Table, Spin, Empty, Pagination } from 'antd'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { useTranslation } from 'react-i18next'
import dayjs, { Dayjs } from 'dayjs'
import type { ColumnsType } from 'antd/es/table'
import { ReportFilters } from './ReportFilters'
import { reportsApi } from '../api'
import type { ReportConfig, ReportType, GroupBy, ReportDataItem, ReportFilterParams } from '../types'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'

interface ReportPanelProps {
  config: ReportConfig
  columns: ColumnsType<any>
  renderChart?: (data: ReportDataItem[]) => React.ReactNode
  additionalFilters?: Record<string, any>
}

/**
 * ReportPanel - Generic report panel with filters, table/chart, and export
 */
export const ReportPanel: React.FC<ReportPanelProps> = ({
  config,
  columns,
  renderChart,
  additionalFilters = {}
}) => {
  const { t } = useTranslation()
  const messageApi = useMessage()

  // Filter state
  const [from, setFrom] = React.useState<Dayjs>(dayjs().startOf('year'))
  const [to, setTo] = React.useState<Dayjs>(dayjs().endOf('year'))
  const [store, setStore] = React.useState<number | undefined>()
  const [groupBy, setGroupBy] = React.useState<GroupBy>('day')

  // Data state
  const [data, setData] = React.useState<ReportDataItem[]>([])
  const [total, setTotal] = React.useState(0)
  const [loading, setLoading] = React.useState(false)
  const [page, setPage] = React.useState(1)
  const [pageSize, setPageSize] = React.useState(50)

  // Build filter params
  const getFilterParams = (): ReportFilterParams => {
    const params: ReportFilterParams = {
      from: from.unix(),
      to: to.unix(),
      ...additionalFilters
    }

    if (config.hasStoreFilter && store) {
      params.store = store
    }

    if (config.hasGroupBy) {
      params.groupBy = groupBy
    }

    if (config.hasPagination) {
      params.page = page
      params.limit = pageSize
    }

    return params
  }

  // Fetch data
  const fetchData = async () => {
    setLoading(true)
    try {
      const response = await reportsApi.getData(config.type, getFilterParams())
      if (response.success) {
        setData(response.data)
        setTotal(response.total ?? response.data.length)
      }
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to fetch report data')))
      setData([])
    } finally {
      setLoading(false)
    }
  }

  // Fetch on mount and when pagination changes
  React.useEffect(() => {
    fetchData()
  }, [page, pageSize])

  // Handle filter
  const handleFilter = () => {
    setPage(1)
    fetchData()
  }

  // Handle export
  const handleExport = () => {
    reportsApi.downloadReport(config.type, getFilterParams())
  }

  // Handle pagination change
  const handlePaginationChange = (newPage: number, newPageSize: number) => {
    setPage(newPage)
    if (newPageSize !== pageSize) {
      setPageSize(newPageSize)
    }
  }

  return (
    <Card
      title={t(config.name, { defaultValue: config.name })}
      style={{ height: '100%' }}
    >
      <ReportFilters
        config={config}
        from={from}
        to={to}
        store={store}
        groupBy={groupBy}
        onFromChange={setFrom}
        onToChange={setTo}
        onStoreChange={setStore}
        onGroupByChange={setGroupBy}
        onFilter={handleFilter}
        onExport={handleExport}
        loading={loading}
      />

      <Spin spinning={loading}>
        {data.length === 0 && !loading ? (
          <Empty description={t('coreshop_report_no_data', { defaultValue: 'No data available' })} />
        ) : renderChart ? (
          // Render chart if provided
          renderChart(data)
        ) : (
          // Render table
          <Table
            columns={columns}
            dataSource={data}
            rowKey={(record, index) => `${config.type}_${index}`}
            pagination={false}
            size="small"
            scroll={{ x: 'max-content' }}
          />
        )}

        {config.hasPagination && total > pageSize && (
          <div style={{ marginTop: 16, textAlign: 'right' }}>
            <Pagination
              current={page}
              pageSize={pageSize}
              total={total}
              onChange={handlePaginationChange}
              showSizeChanger
              showTotal={(total) => `${total} ${t('items', { defaultValue: 'items' })}`}
            />
          </div>
        )}
      </Spin>
    </Card>
  )
}
