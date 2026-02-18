import React from 'react'
import {SplitLayout} from '@pimcore/studio-ui-bundle/components'
import { EntityList } from './EntityList'
import {EntityDetail} from './EntityDetail'
import type {EntityListItem} from '../types'

export interface EntityManagerProps<TDetail extends Record<string, any>> {
    items: EntityListItem[]
    selectedId?: number
    detail?: TDetail
    dirty?: boolean
    loadingList?: boolean
    loadingDetail?: boolean
    onReloadList: () => void
    onAdd: () => void
    onDelete: (id: number) => void
    onSelect: (id: number) => void
    onReloadDetail: () => void
    onSave: () => void
    renderDetail: (data: TDetail | undefined, setData: (draft: Partial<TDetail>) => void) => React.ReactNode
}

export function EntityManager<TDetail extends Record<string, any>>(props: EntityManagerProps<TDetail>): React.JSX.Element {
    const left = {
        id: 'entity-list',
        size: 25,
        minSize: 220,
        children: [
            <EntityList
                key='entity-list'
                addLabel={'New'}
                items={props.items}
                onReload={props.onReloadList}
                onAdd={props.onAdd}
                onDelete={props.onDelete}
                onSelect={props.onSelect}
                rootTitle='Items'
            />
        ]
    }

    const right = {
        id: 'entity-detail',
        size: 75,
        minSize: 400,
        padding: 10,
        children: [
            <EntityDetail
                key='entity-detail'
                data={props.detail}
                dirty={props.dirty}
                loading={props.loadingDetail}
                onReload={props.onReloadDetail}
                onSave={props.onSave}
                render={props.renderDetail}
            />
        ]
    }

    return (
        <SplitLayout
            leftItem={left}
            rightItem={right}
            withDivider
            withToolbar
        />
    )
}
