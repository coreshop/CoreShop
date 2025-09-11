import React, { createContext, useContext, useMemo } from 'react'
import { Form } from 'antd'

export interface LocalizedFieldsContextValue {
  locales: string[]
}

const LocalizedFieldsContext = createContext<LocalizedFieldsContextValue | undefined>(undefined)

export const LocalizedFieldsProvider: React.FC<{ locales: string[], children: React.ReactNode }> = ({ locales, children }) => {
  const value = useMemo(() => ({ locales }), [locales])
  return (
    <LocalizedFieldsContext.Provider value={ value }>
      {children}
    </LocalizedFieldsContext.Provider>
  )
}

export const useLocalizedFields = (): LocalizedFieldsContextValue | undefined => useContext(LocalizedFieldsContext)

export const withLocalizedFieldsLocale = (Component: typeof Form.Item): typeof Form.Item => {
  const Wrapped = (props: React.ComponentProps<typeof Form.Item>) => {
    const ctx = useLocalizedFields()
    if (!ctx) return <Component { ...props } />

    const { locales } = ctx
    const name = Array.isArray(props.name) ? props.name : [props.name]
    const primary = locales[0]
    const computedName = [...name, primary]
    const computedLabel = (
      <>
        {props.label} <span style={{ color: 'var(--ant-color-text-secondary)' }}>({primary?.toUpperCase?.()})</span>
      </>
    )

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const { label, name: _n, ...rest } = props as any
    return <Component { ...rest } label={ computedLabel } name={ computedName } />
  }

  const NewFormItem = Wrapped as typeof Form.Item
  NewFormItem.useStatus = Component.useStatus
  return NewFormItem
}

