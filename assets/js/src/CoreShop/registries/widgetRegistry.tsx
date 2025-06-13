import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import type { WidgetRegistry } from '@pimcore/studio-ui-bundle/modules/widget-manager';
import { CoreShopMainPage } from '../components/coreshop-page';
import { CoreshopCountriesPage } from '../components/countries/coreshop-countries-page';
import { CoreShopStatesPage} from "../components/states/coreshop-states-page";
import {CoreshopCurrenciesPage} from "../components/currencies/coreshop-currencies-page";

export function registerWidgets() {
    const widgetRegistryService = container.get<WidgetRegistry>(serviceIds.widgetManager);
    widgetRegistryService.registerWidget({
        name: 'CoreShopMainPage',
        component: CoreShopMainPage
    });

    widgetRegistryService.registerWidget({
        name: 'CoreShopCountriesPage',
        component: CoreshopCountriesPage
    });

    widgetRegistryService.registerWidget({
        name: 'CoreShopStatesPage',
        component: CoreShopStatesPage
    });

    widgetRegistryService.registerWidget({
        name: 'CoreShopCurrenciesPage',
        component: CoreshopCurrenciesPage
    });
}
