import React from 'react'
import { type AbstractModule, Icon, type FolderTabManager, container, serviceIds, WidgetRegistry } from 'pimcore-studio-ui'
import { MyFirstTabComponent } from './my-first-tab-component';
import { MyFirstWidget } from './my-first-widget';

export const FolderTabExtension: AbstractModule = {
    onInit: (): void => {
        // registration of our new widget
        const widgetManager = container.get<WidgetRegistry>(serviceIds.widgetManager)

        widgetManager.registerWidget({
            name: 'my-first-widget',
            component: MyFirstWidget
        })

        const tabManager = container.get<FolderTabManager>(serviceIds['Asset/Editor/FolderTabManager'])

        tabManager.register({
            children: <MyFirstTabComponent />,
            icon: <Icon value={ 'camera' } />,
            key: 'my-first-tab-component',
            label: '1. tab component'
        })
    }
}