/**
 * CoreShop Menu Extension Module
 *
 * Dynamically registers CoreShop navigation items from backend API with Pimcore Studio UI
 */

import {container} from '@pimcore/studio-ui-bundle'
import {serviceIds} from '@pimcore/studio-ui-bundle/app'
import {IMainNavItem, type MainNavRegistry} from '@pimcore/studio-ui-bundle/modules/app'
import {type WidgetRegistry} from '@pimcore/studio-ui-bundle/modules/widget-manager'
import {type IconLibrary} from '@pimcore/studio-ui-bundle/modules/icon-library'
import {menuService} from '../services/MenuService'
import {CoreShopMenuItem} from '../types'
import {CoreShopWidget} from '../components/CoreShopWidget'
import {MenuButtonRegistry, consumeQueuedMenuButtons} from "../services/button-registry";
import React from "react";

export const CoreShopMenuExtension = {
    onInit(): void {
        const mainNavRegistryService = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)
        const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)

        container.bind('CoreShopMenuButtons').to(MenuButtonRegistry).inSingletonScope()
        const buttonRegistry = container.get<MenuButtonRegistry>('CoreShopMenuButtons')
        consumeQueuedMenuButtons().forEach((item) => buttonRegistry.add(item))

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

            if (menuItems && menuItems.length > 0) {
                // Register each top-level menu item
                for (const item of menuItems) {
                    this.registerMenuItem(item, mainNavRegistry, widgetRegistry)
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

        // If content is available (SVG logo), dynamically create and register icon
        if (item.content) {
            const iconName = `coreshop_dynamic_${item.id}`
            const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)

            const icon = ()=>{
                return (
                    <div style={{
                        width: '100%'
                    }} dangerouslySetInnerHTML={{__html: item.content ?? ''}} />
                )
            }

            // Register in icon library
            iconLibrary.register({
                name: iconName,
                component: icon
            })

            navItem.icon = iconName;
        } else if (item.icon) {
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
                const buttonConfig = buttonRegistry.get(item.widgetButton);

                if (buttonConfig) {
                    navItem.button = (({ closeMainNav }: { closeMainNav: () => void }) => React.createElement(buttonConfig.button, {
                        icon: item.icon!,
                        label: item.label,
                        closeMainNav,
                    })) as any

                    mainNavRegistry.registerMainNavItem(navItem)
                    return;
                }
            }

            navItem.widgetConfig.component = widgetId;

            mainNavRegistry.registerMainNavItem(navItem)

            // Only register a fallback widget if one isn't already registered
            // (e.g. StudioForm Demos registers its own widget)
            if (!widgetRegistry.getWidget(widgetId)) {
                widgetRegistry.registerWidget({
                    name: widgetId,
                    component: props => CoreShopWidget({item}),
                })
            }
        }
    }
}
