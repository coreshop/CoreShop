import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager';
import { CoreShopMainPage } from '../components/coreshop-page';
import { CoreShopCountriesPage } from '../components/coreshop-countries-page';

export function registerWidgets() {
    const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager);
    widgetRegistryService.registerWidget({
        name: 'CoreShopMainPage',
        component: CoreShopMainPage
    });

    widgetRegistryService.registerWidget({
        name: 'CoreShopCountriesPage',
        component: CoreShopCountriesPage
    });
}
