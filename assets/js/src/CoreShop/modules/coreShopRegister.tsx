import { AbstractModule } from '@pimcore/studio-ui-bundle';
import { registerIcons } from '../registries/iconRegistry';
import { registerComponents } from '../registries/componentRegistry';
import { registerMainNav } from '../registries/mainNavRegistry';
import { registerWidgets } from '../registries/widgetRegistry';

export const CoreShopRegister: AbstractModule = {
    onInit: () => {
        registerIcons();
        registerComponents();
        registerMainNav();
        registerWidgets();
    }
};
