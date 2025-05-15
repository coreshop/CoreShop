import { type AbstractModule, container } from 'pimcore-studio-ui';
import { serviceIds } from 'pimcore-studio-ui/app';
import { componentConfig, type ComponentRegistry } from 'pimcore-studio-ui/modules/app';
import { CoreShopButton } from '../components/coreshop-button';
import { type WidgetRegistry } from 'pimcore-studio-ui/modules/widget-manager';
import { CoreShopMainPage } from '../components/coreshop-page';
import { type IconLibrary } from 'pimcore-studio-ui/modules/icon-library';
import { CoreShopIcon } from "../Icons/coreshop";

export const CoreShopRegister: AbstractModule = {
    onInit: (): void => {
        const componentRegistry = container.get<ComponentRegistry>(serviceIds['App/ComponentRegistry/ComponentRegistry'])

        // REGISTER ICON
        const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary)
        iconLibrary.register({
            name: 'coreshop-icon',
            component: CoreShopIcon
        })


        // LEFT SIDEBAR
        componentRegistry.registerToSlot(
            componentConfig.leftSidebar.slot.name,
            {
                name: 'coreShopButton',
                component: CoreShopButton,
                priority: 101
            }
        )


        // MAIN TAB
        const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager)
        widgetRegistryService.registerWidget({
            name: 'CoreShopMainPage',
            component: CoreShopMainPage
        })
    }
}

