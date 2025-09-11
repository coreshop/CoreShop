import React from 'react'
// Thin wrapper to manage local state and render provided detail content.
// Intentionally no ContentLayout or Toolbar here; those are handled by the parent container
// to keep the bottom toolbar sticky like in Pimcore ManagementDetail.
import {useTranslation} from 'react-i18next'

export interface EntityDetailProps<T> {
    id?: number
    data?: T
    dirty?: boolean
    loading?: boolean
    onReload?: () => void
    onSave?: () => void
    render: (data: T | undefined, setData: (draft: Partial<T>) => void) => React.ReactNode
}

export function EntityDetail<T extends Record<string, any>>({
                                                                id,
                                                                data,
                                                                dirty,
                                                                loading,
                                                                onReload,
                                                                onSave,
                                                                render
                                                            }: EntityDetailProps<T>): React.JSX.Element {
    const {t} = useTranslation()
    const [local, setLocal] = React.useState<T | undefined>(data)

    React.useEffect(() => {
        setLocal(data)
    }, [id, data])

    return (<>{render(local, (draft) => setLocal({...(local as any), ...draft}))}</>)
}
