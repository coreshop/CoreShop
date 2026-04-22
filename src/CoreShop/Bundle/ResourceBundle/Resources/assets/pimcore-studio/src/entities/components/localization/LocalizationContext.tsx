import React from 'react'
import { useStudioLanguages } from '../../../components/localization/useStudioLanguages'

interface LocalizationState {
  locales: string[]
  currentLocale: string
  setCurrentLocale: (loc: string) => void
}

const LocalizationContext = React.createContext<LocalizationState | undefined>(undefined)

export const LocalizationProvider: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const locales = useStudioLanguages()
  const [currentLocale, setCurrentLocale] = React.useState<string>(locales[0] ?? 'en')

  React.useEffect(() => {
    if (!locales.includes(currentLocale) && locales.length > 0) setCurrentLocale(locales[0]!)
  }, [locales])

  return (
    <LocalizationContext.Provider value={{ locales, currentLocale, setCurrentLocale }}>
      {children}
    </LocalizationContext.Provider>
  )
}

export const useLocalization = (): LocalizationState => {
  const ctx = React.useContext(LocalizationContext)
  if (!ctx) throw new Error('useLocalization must be used within LocalizationProvider')
  return ctx
}

