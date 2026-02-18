/**
 * CoreShop OrderBundle - Order Creation Context
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, {
  createContext,
  useContext,
  useReducer,
  useCallback,
  useEffect,
  useRef,
  useMemo
} from 'react'
import { container } from '@pimcore/studio-ui-bundle'
import { useMessage } from '@pimcore/studio-ui-bundle/components'
import { getErrorMessage, renderApiError } from '@coreshop/resource/src/entities'
import type {
  OrderCreationState,
  OrderCreationAction,
  OrderCreationFormData,
  CustomerDetails,
  OrderCreationRequest
} from '../types'
import { orderCreationApi } from '../api'
import type { OrderCreationStepRegistry } from '../registry/OrderCreationStepRegistry'
import { orderCreationServiceIds } from '../service-ids'

// Initial state
const initialFormData: OrderCreationFormData = {
  store: null,
  currency: null,
  localeCode: null,
  items: [],
  shippingAddress: null,
  invoiceAddress: null,
  carrier: null
}

const initialState: OrderCreationState = {
  customerId: null,
  customerDetails: null,
  formData: initialFormData,
  preview: null,
  previewLoading: false,
  previewError: null,
  stepValidation: {},
  creating: false,
  createError: null,
  resetKey: 0
}

// Reducer
function orderCreationReducer(
  state: OrderCreationState,
  action: OrderCreationAction
): OrderCreationState {
  switch (action.type) {
    case 'SET_CUSTOMER':
      return {
        ...state,
        customerId: action.payload.id,
        customerDetails: action.payload.details
      }

    case 'UPDATE_FORM_DATA':
      return {
        ...state,
        formData: { ...state.formData, ...action.payload }
      }

    case 'SET_PREVIEW_LOADING':
      return { ...state, previewLoading: action.payload }

    case 'SET_PREVIEW':
      return {
        ...state,
        preview: action.payload,
        previewLoading: false,
        previewError: null
      }

    case 'SET_PREVIEW_ERROR':
      return {
        ...state,
        previewError: action.payload,
        previewLoading: false
      }

    case 'SET_STEP_VALIDATION':
      return {
        ...state,
        stepValidation: {
          ...state.stepValidation,
          [action.payload.step]: action.payload.valid
        }
      }

    case 'SET_CREATING':
      return { ...state, creating: action.payload }

    case 'SET_CREATE_ERROR':
      return { ...state, createError: action.payload, creating: false }

    case 'RESET':
      return {
        ...initialState,
        customerId: state.customerId,
        customerDetails: state.customerDetails,
        resetKey: state.resetKey + 1
      }

    case 'FULL_RESET':
      return initialState

    default:
      return state
  }
}

// Context type
interface OrderCreationContextType {
  state: OrderCreationState
  dispatch: React.Dispatch<OrderCreationAction>
  triggerPreview: () => void
  loadCustomer: (customerId: number) => Promise<void>
  createSale: (saleType: 'cart' | 'order' | 'quote', name?: string) => Promise<number | null>
  reset: () => void
  fullReset: () => void
  isValid: () => boolean
}

const OrderCreationContext = createContext<OrderCreationContextType | null>(null)

export const useOrderCreation = (): OrderCreationContextType => {
  const context = useContext(OrderCreationContext)
  if (!context) {
    throw new Error('useOrderCreation must be used within OrderCreationProvider')
  }
  return context
}

interface OrderCreationProviderProps {
  children: React.ReactNode
  initialCustomerId?: number
}

export const OrderCreationProvider: React.FC<OrderCreationProviderProps> = ({
  children,
  initialCustomerId
}) => {
  const [state, dispatch] = useReducer(orderCreationReducer, initialState)
  const previewTimeoutRef = useRef<ReturnType<typeof setTimeout> | null>(null)
  const messageApi = useMessage()
  // Use ref to always access current state in async callbacks
  const stateRef = useRef(state)
  stateRef.current = state

  // Get step registry
  const stepRegistry = useMemo(() => {
    if (container.isBound(orderCreationServiceIds.stepRegistry)) {
      return container.get<OrderCreationStepRegistry>(orderCreationServiceIds.stepRegistry)
    }
    return null
  }, [])

  // Load customer details
  const loadCustomer = useCallback(async (customerId: number) => {
    try {
      const details = await orderCreationApi.getCustomerDetails(customerId)
      dispatch({ type: 'SET_CUSTOMER', payload: { id: customerId, details } })
    } catch (error) {
      void messageApi.error(renderApiError(getErrorMessage(error, 'Failed to load customer')))
    }
  }, [messageApi])

  // Load initial customer if provided
  useEffect(() => {
    if (initialCustomerId && !state.customerId) {
      void loadCustomer(initialCustomerId)
    }
  }, [initialCustomerId, state.customerId, loadCustomer])

  // Debounced preview trigger
  const triggerPreview = useCallback(() => {
    // Clear existing timeout
    if (previewTimeoutRef.current) {
      clearTimeout(previewTimeoutRef.current)
    }

    // Don't preview without customer or registry
    if (!stateRef.current.customerId || !stepRegistry) return

    // Check minimum requirements
    if (!stateRef.current.formData.store || !stateRef.current.formData.currency || !stateRef.current.formData.localeCode) {
      return
    }

    // Debounce preview calls (300ms)
    previewTimeoutRef.current = setTimeout(async () => {
      dispatch({ type: 'SET_PREVIEW_LOADING', payload: true })

      try {
        // Use ref to get current state (not stale closure value)
        const currentState = stateRef.current
        // Collect values from all steps
        const steps = stepRegistry.getSorted()
        let requestData: Record<string, unknown> = {
          customer: currentState.customerId
        }

        for (const step of steps) {
          const stepValues = step.getValues(currentState)
          requestData = { ...requestData, ...stepValues }
        }

        const preview = await orderCreationApi.preview(requestData)
        dispatch({ type: 'SET_PREVIEW', payload: preview })

        // Notify steps about preview data
        for (const step of steps) {
          if (step.onPreviewData) {
            step.onPreviewData(preview, dispatch)
          }
        }
      } catch (error) {
        dispatch({
          type: 'SET_PREVIEW_ERROR',
          payload: error instanceof Error ? error.message : 'Preview failed'
        })
      }
    }, 300)
  }, [stepRegistry])

  // Check if all required steps are valid
  const isValid = useCallback((): boolean => {
    if (!stepRegistry) return false

    const steps = stepRegistry.getSorted()
    return steps.every((step) => {
      if (step.isVisible && !step.isVisible(state)) return true // Skip hidden steps
      return step.isValid(state)
    })
  }, [state, stepRegistry])

  // Create sale
  const createSale = useCallback(
    async (saleType: 'cart' | 'order' | 'quote', name?: string): Promise<number | null> => {
      if (!isValid() || !stepRegistry || !state.customerId) return null

      dispatch({ type: 'SET_CREATING', payload: true })

      try {
        // Collect values from all steps
        const steps = stepRegistry.getSorted()
        let requestData: Record<string, unknown> = {
          customer: state.customerId,
          saleType
        }

        // Add name for carts
        if (name) {
          requestData.name = name
        }

        for (const step of steps) {
          const stepValues = step.getValues(state)
          requestData = { ...requestData, ...stepValues }
        }

        const result = await orderCreationApi.create(requestData as OrderCreationRequest)
        dispatch({ type: 'SET_CREATING', payload: false })
        return result.id
      } catch (error) {
        dispatch({
          type: 'SET_CREATE_ERROR',
          payload: error instanceof Error ? error.message : 'Create failed'
        })
        return null
      }
    },
    [state, isValid, stepRegistry]
  )

  // Reset (keep customer)
  const reset = useCallback(() => {
    dispatch({ type: 'RESET' })
  }, [])

  // Full reset (clear customer too)
  const fullReset = useCallback(() => {
    dispatch({ type: 'FULL_RESET' })
  }, [])

  // Clean up timeout on unmount
  useEffect(() => {
    return () => {
      if (previewTimeoutRef.current) {
        clearTimeout(previewTimeoutRef.current)
      }
    }
  }, [])

  return (
    <OrderCreationContext.Provider
      value={{
        state,
        dispatch,
        triggerPreview,
        loadCustomer,
        createSale,
        reset,
        fullReset,
        isValid
      }}
    >
      {children}
    </OrderCreationContext.Provider>
  )
}
