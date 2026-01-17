/**
 * CoreShop OrderBundle - Sale Context
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 */

import React, { createContext, useContext, useState, useMemo, useCallback, type ReactNode } from 'react'
import type { Sale } from '../types'
import { ButtonRegistry } from './ButtonRegistry'

interface SaleContextType {
  sale: Sale | null
  readonly: boolean
  onChange: (updates: Partial<Sale>) => void
  onReload: () => void
  // Dynamic action system - generic for ANY action
  isActionOpen: (actionKey: string) => boolean
  openAction: (actionKey: string) => void
  closeAction: (actionKey: string) => void
  // Button registry - tabs can add their own toolbar buttons
  buttonRegistry: ButtonRegistry
}

const SaleContext = createContext<SaleContextType | null>(null)

export const useSaleContext = (): SaleContextType => {
  const context = useContext(SaleContext)
  if (!context) {
    throw new Error('useSaleContext must be used within SaleContextProvider')
  }
  return context
}

interface SaleContextProviderProps {
  sale: Sale
  onChange: (updates: Partial<Sale>) => void
  onReload: () => void
  children: ReactNode
}

export const SaleContextProvider: React.FC<SaleContextProviderProps> = ({
  sale,
  onChange,
  onReload,
  children
}) => {
  const readonly = sale ? !sale.editable : true

  // Dynamic action states - Map of actionKey -> isOpen
  // This supports ANY action key, registered by ANY bundle/tab
  const [actionStates, setActionStates] = useState<Record<string, boolean>>({})

  // Button registry - create once per sale detail instance
  const buttonRegistry = useMemo(() => new ButtonRegistry(), [])

  // Generic action system - works for any action key
  // Memoize to prevent unnecessary re-renders
  const isActionOpen = useCallback((actionKey: string): boolean => {
    return actionStates[actionKey] ?? false
  }, [actionStates])

  const openAction = useCallback((actionKey: string): void => {
    setActionStates(prev => ({ ...prev, [actionKey]: true }))
  }, [])

  const closeAction = useCallback((actionKey: string): void => {
    setActionStates(prev => ({ ...prev, [actionKey]: false }))
  }, [])

  // Memoize context value to prevent unnecessary re-renders of consumers
  const contextValue = useMemo<SaleContextType>(() => ({
    sale,
    readonly,
    onChange,
    onReload,
    isActionOpen,
    openAction,
    closeAction,
    buttonRegistry
  }), [sale, readonly, onChange, onReload, isActionOpen, openAction, closeAction, buttonRegistry])

  return (
    <SaleContext.Provider value={contextValue}>
      {children}
    </SaleContext.Provider>
  )
}
