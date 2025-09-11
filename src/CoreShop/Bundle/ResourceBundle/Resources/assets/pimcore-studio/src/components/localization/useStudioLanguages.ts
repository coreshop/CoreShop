import React from 'react'

export function useStudioLanguages(): string[] {
  const [langs, setLangs] = React.useState<string[]>(['en'])

  React.useEffect(() => {
    const tryFetch = async () => {
      try {
        const res = await fetch('/pimcore-studio/api/settings', { credentials: 'same-origin' })
        if (res.ok) {
          const json = await res.json()
          const req = Array.isArray(json?.validLanguages) ? json.validLanguages as string[] : []
          if (req.length) setLangs(req)
        }
      } catch {}
    }
    void tryFetch()
  }, [])

  return langs
}
