/**
 * CoreShop Menu Extension Module
 *
 * Dynamically registers CoreShop navigation items from backend API with Pimcore Studio UI
 */

// @ts-ignore
import {container} from '@pimcore/studio-ui-bundle'
// @ts-ignore
import {serviceIds} from '@pimcore/studio-ui-bundle/app'
// @ts-ignore
import {type MainNavRegistry, type WidgetRegistry} from '@pimcore/studio-ui-bundle/modules/app'
import {menuService} from '../services/MenuService'
import {CoreShopMenuItem} from '../types'
import {CoreShopWidget} from '../components/CoreShopWidget'

export const CoreShopMenuExtension = {
    onInit(): void {
        const mainNavRegistryService = container.get<MainNavRegistry>(serviceIds.mainNavRegistry)
        const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)

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
                console.log(`[CoreShop Menu Extension] Successfully registered ${menuItems.length} navigation items from all menu types`)
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
        const navItem: any = {
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
                component: widgetId,
                config: {
                    icon: {
                        type: 'name',
                        value: item.icon || 'coreshop_nav_icon_default'
                    }
                }
            }

            mainNavRegistry.registerMainNavItem(navItem)

            if (widgetRegistry.getWidget(item.widgetId)) {
                return;
            }

            //TODO: should be removed once every widget is implemented!
            widgetRegistry.registerWidget({
                name: widgetId,
                component: CoreShopWidget({item})
            })
        }
    }
}