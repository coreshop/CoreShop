import { useSettings } from '@pimcore/studio-ui-bundle/modules/app'
import { useMemo } from 'react'

export function useStudioLanguages(): string[] {
  const settings = useSettings()

  return useMemo(() => {
    const langs = Array.isArray(settings?.validLanguages) ? settings.validLanguages as string[] : []
    return langs.length > 0 ? langs : ['en']
  }, [settings?.validLanguages])
}
