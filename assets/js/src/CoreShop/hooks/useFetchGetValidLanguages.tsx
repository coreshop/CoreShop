import { useEffect, useState } from 'react'

export const useFetchGetValidLanguages = (): { languages: string[]; loading: boolean } => {
    const [languages, setLanguages] = useState<string[]>([])
    const [loading, setLoading] = useState(true)

    useEffect(() => {
        const fetchLanguages = async () => {
            try {
                const res = await fetch('/pimcore-studio/api/settings')
                const data = await res.json()
                setLanguages(data?.validLanguages ?? [])
            } catch (err) {
                console.error('Failed to fetch system settings:', err)
            } finally {
                setLoading(false)
            }
        }

        fetchLanguages()
    }, [])

    return { languages, loading }
}
