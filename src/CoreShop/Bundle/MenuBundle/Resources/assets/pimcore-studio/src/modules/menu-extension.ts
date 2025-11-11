/**
 * CoreShop Menu Extension Module
 *
 * Dynamically registers CoreShop navigation items from backend API with Pimcore Studio UI
 */

import {container} from '@pimcore/studio-ui-bundle'
import {serviceIds} from '@pimcore/studio-ui-bundle/app'
import {IMainNavItem, type MainNavRegistry} from '@pimcore/studio-ui-bundle/modules/app'
import { type WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager'
import {menuService} from '../services/MenuService'
import {CoreShopMenuItem} from '../types'
import {CoreShopWidget} from '../components/CoreShopWidget'
import {MenuButtonRegistry} from "../services/button-registry";
import React from "react";

export const CoreShopMenuExtension = {
    onInit(): void {
        const mainNavRegistryService = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)
        const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)

        container.bind('CoreShopMenuButtons').to(MenuButtonRegistry).inSingletonScope()

        // Load and register menu items dynamically
        this.loadAndRegisterMenuItems(mainNavRegistryService, widgetRegistryService)
    },

    async loadAndRegisterMenuItems(
        mainNavRegistry: MainNavRegistry,
        widgetRegistry: WidgetRegistry
    ): Promise<void> {
        try {
            // Load ALL CoreShop menu structures from backend
            const menuItems = await menuService.getAllMenuStructures()

            if (menuItems) {
                // Register each top-level menu item
                for (const key in menuItems) {
                    const items = menuItems[key];

                    for (const item of items) {
                        this.registerMenuItem(item, mainNavRegistry, widgetRegistry, key)
                    }
                }
            }
        } catch (error) {
            console.error('[CoreShop Menu Extension] Failed to load menu structures:', error)
        }
    },

    registerMenuItem(
        item: CoreShopMenuItem,
        mainNavRegistry: MainNavRegistry,
        widgetRegistry: WidgetRegistry,
        parentPath: string = ''
    ): void {
        const fullPath = parentPath ? `${parentPath}/${item.label}` : item.label

        // Register main navigation item
        const navItem: IMainNavItem = {
            path: fullPath
        }

        if (item.icon) {
            navItem.icon = item.icon
        }

        // If item has children, register them recursively
        if (item.children && item.children.length > 0) {
            mainNavRegistry.registerMainNavItem(navItem)

            // Register children recursively
            for (const child of item.children) {
                this.registerMenuItem(child, mainNavRegistry, widgetRegistry, fullPath)
            }
        } else {
            // Leaf item - register with widget
            const widgetId = item.widgetId || `coreshop-${item.id}`

            navItem.widgetConfig = {
                name: item.label,
                id: widgetId,
                config: {
                    icon: {
                        type: 'name',
                        value: item.icon || 'coreshop_nav_icon_default'
                    }
                }
            }

            if (item.widgetEvent) {
                navItem.onClick = () => {
                    const event = new CustomEvent(item.widgetEvent!)
                    window.dispatchEvent(event)
                }

                mainNavRegistry.registerMainNavItem(navItem)

                return;
            }

            if (item.widgetButton) {
                const buttonRegistry = container.get<MenuButtonRegistry>('CoreShopMenuButtons');
                const button = buttonRegistry.get(item.widgetButton);

                if (button) {
                    navItem.button = () => React.createElement(button.button, {
                        icon: item.icon!,
                        label: item.label
                    })

                    mainNavRegistry.registerMainNavItem(navItem)
                }

                return;
            }

            navItem.widgetConfig.component = widgetId;

            mainNavRegistry.registerMainNavItem(navItem)

            if (!item.widgetId || !widgetRegistry.getWidget(item.widgetId)) {
                widgetRegistry.registerWidget({
                    name: widgetId,
                    component: props => CoreShopWidget({item}),
                })

                return;
            }

            widgetRegistry.registerWidget({
                name: widgetId,
                component: props => CoreShopWidget({item}),
            })
        }
    }
}