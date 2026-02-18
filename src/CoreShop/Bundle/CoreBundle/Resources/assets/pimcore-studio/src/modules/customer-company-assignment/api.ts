/**
 * CoreShop Customer Company Assignment API
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

export interface EntityDetails {
  type: 'customer' | 'company'
  name: string
  id: number
}

export interface ValidationData {
  addresses: Array<{ id: number; path: string }>
}

export interface DuplicateCompany {
  id: number
  name: string
  path: string
}

export interface ApiResponse<T> {
  success: boolean
  message?: string
  data?: T
}

export interface AssignmentResponse {
  success: boolean
  message?: string
  formError?: boolean
  customerId: number
  companyId: number
}

const BASE_URL = '/pimcore-studio/api/coreshop/customer-company-modifier'

export const customerCompanyApi = {
  async getEntityDetails(type: 'customer' | 'company', objectId: number): Promise<ApiResponse<EntityDetails>> {
    const response = await fetch(`${BASE_URL}/get-entity-details/${type}/${objectId}`)
    return response.json()
  },

  async validateAssignment(customerId: number, companyId?: number): Promise<ApiResponse<ValidationData>> {
    const url = companyId
      ? `${BASE_URL}/validate-assignment/${customerId}/${companyId}`
      : `${BASE_URL}/validate-assignment/${customerId}`
    const response = await fetch(url)
    return response.json()
  },

  async checkDuplicateNames(value: string): Promise<{ success: boolean; list: DuplicateCompany[] }> {
    const response = await fetch(`${BASE_URL}/duplication-name-check?value=${encodeURIComponent(value)}`)
    return response.json()
  },

  async dispatchNewAssignment(
    customerId: number,
    data: {
      addressAssignmentType: string
      addressAccessType: string
      newCompanyName: string
    }
  ): Promise<AssignmentResponse> {
    const formData = new URLSearchParams()
    formData.append('addressAssignmentType', data.addressAssignmentType)
    formData.append('addressAccessType', data.addressAccessType)
    formData.append('newCompanyName', data.newCompanyName)

    const response = await fetch(`${BASE_URL}/dispatch-new-assignment/${customerId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData.toString(),
    })
    return response.json()
  },

  async dispatchExistingAssignment(
    customerId: number,
    companyId: number,
    data: {
      addressAssignmentType: string
      addressAccessType: string
    }
  ): Promise<AssignmentResponse> {
    const formData = new URLSearchParams()
    formData.append('addressAssignmentType', data.addressAssignmentType)
    formData.append('addressAccessType', data.addressAccessType)

    const response = await fetch(`${BASE_URL}/dispatch-existing-assignment/${customerId}/${companyId}`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: formData.toString(),
    })
    return response.json()
  },
}
