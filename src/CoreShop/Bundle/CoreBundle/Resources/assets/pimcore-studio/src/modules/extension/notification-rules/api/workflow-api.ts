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

export interface WorkflowState {
  color: string
  label: string
  state: string
}

export interface WorkflowTransition {
  name: string
  froms: string[]
  tos: string[]
}

export interface WorkflowStatesResponse {
  success: boolean
  states: Record<string, WorkflowState[]>
  transitions: Record<string, WorkflowTransition[]>
}

// Module-level cache
let cachedData: WorkflowStatesResponse | null = null
let loadPromise: Promise<WorkflowStatesResponse> | null = null

const loadWorkflowStates = async (): Promise<WorkflowStatesResponse> => {
  if (cachedData) return cachedData
  if (loadPromise) return loadPromise

  loadPromise = (async () => {
    try {
      const response = await fetch('/admin/coreshop/order/get-states')
      const data = await response.json() as WorkflowStatesResponse

      if (data.success) {
        cachedData = data
        return data
      }

      throw new Error('Failed to load workflow states')
    } catch (err) {
      console.error('Failed to load workflow states:', err)
      // Return empty data on error
      return {
        success: false,
        states: {},
        transitions: {}
      }
    } finally {
      loadPromise = null
    }
  })()

  return loadPromise
}

export const clearWorkflowCache = () => {
  cachedData = null
  loadPromise = null
}

/**
 * Get states for a specific workflow
 */
export const getWorkflowStates = async (workflowName: string): Promise<WorkflowState[]> => {
  const data = await loadWorkflowStates()
  return data.states[workflowName] ?? []
}

/**
 * Get transitions for a specific workflow
 */
export const getWorkflowTransitions = async (workflowName: string): Promise<WorkflowTransition[]> => {
  const data = await loadWorkflowStates()
  return data.transitions[workflowName] ?? []
}

/**
 * Available workflow names (matching PHP identifiers)
 */
export const WorkflowNames = {
  ORDER: 'coreshop_order',
  ORDER_PAYMENT: 'coreshop_order_payment',
  ORDER_SHIPMENT: 'coreshop_order_shipment',
  ORDER_INVOICE: 'coreshop_order_invoice',
  ORDER_SALES_TYPE: 'coreshop_order_sales_type',
  PAYMENT: 'coreshop_payment',
  INVOICE: 'coreshop_invoice',
  SHIPMENT: 'coreshop_shipment',
  QUOTE: 'coreshop_quote'
} as const

export type WorkflowName = typeof WorkflowNames[keyof typeof WorkflowNames]
