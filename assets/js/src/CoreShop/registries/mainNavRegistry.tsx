import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import type { MainNavRegistry } from '@pimcore/studio-ui-bundle/modules/app';

export function registerMainNav() {
    const mainNavRegistryService = container.get<MainNavRegistry>(serviceIds.mainNavRegistry);

    mainNavRegistryService.registerMainNavItem({
        path: 'Coreshop',
        icon: 'coreshop-icon'
    });

    mainNavRegistryService.registerMainNavItem({
        path: 'Coreshop/Dummy',
        widgetConfig: {
            name: 'CoreShopMainPage',
            component: 'CoreShopMainPage',
            config: {
                icon: {
                    type: 'name',
                    value: 'coreshop-icon'
                }
            }
        }
    });

    mainNavRegistryService.registerMainNavItem({
        path: 'Coreshop/Localization/Countries',
        widgetConfig: {
            name: 'CoreShopCountriesPage',
            component: 'CoreShopCountriesPage',
            config: {
                icon: {
                    type: 'name',
                    value: 'coreshop-icon'
                }
            }
        }
    });
}
