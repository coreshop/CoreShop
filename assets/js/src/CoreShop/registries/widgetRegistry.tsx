import { container } from 'pimcore-studio-ui';
import { serviceIds } from 'pimcore-studio-ui/app';
import type { WidgetRegistry } from 'pimcore-studio-ui/modules/widget-manager';
import { CoreShopMainPage } from '../components/coreshop-page';
import { CoreShopCountriesPage } from '../components/coreshop-countries-page';
import { CoreShopCountryDetailPage} from "../components/coreshop-country-detail-page";

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

    widgetRegistryService.registerWidget({
        name: 'CoreShopCountryDetailPage',
        component: CoreShopCountryDetailPage
    });
}
