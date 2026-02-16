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

import type { ReportType, ReportFilterParams, ReportResponse } from './types'

/**
 * Build URL params from filter object
 */
const buildParams = (params: ReportFilterParams): string => {
  const searchParams = new URLSearchParams()

  for (const [key, value] of Object.entries(params)) {
    if (value !== undefined && value !== null) {
      if (Array.isArray(value)) {
        searchParams.set(key, JSON.stringify(value))
      } else {
        searchParams.set(key, String(value))
      }
    }
  }

  return searchParams.toString()
}

/**
 * Reports API
 */
export const reportsApi = {
  /**
   * Fetch report data
   */
  async getData(reportType: ReportType, params: ReportFilterParams): Promise<ReportResponse> {
    const queryString = buildParams({ ...params, report: reportType })
    const response = await fetch(`/pimcore-studio/api/coreshop/report/get-data?${queryString}`)

    if (!response.ok) {
      throw new Error(`Failed to fetch report data: ${response.statusText}`)
    }

    return response.json()
  },

  /**
   * Get export URL for downloading report
   */
  getExportUrl(reportType: ReportType, params: ReportFilterParams): string {
    const queryString = buildParams({ ...params, report: reportType })
    return `/pimcore-studio/api/coreshop/report/export?${queryString}`
  },

  /**
   * Download report as file
   */
  downloadReport(reportType: ReportType, params: ReportFilterParams): void {
    const url = reportsApi.getExportUrl(reportType, params)
    window.open(url, '_blank')
  }
}
