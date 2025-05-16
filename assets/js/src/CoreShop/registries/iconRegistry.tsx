import { container } from 'pimcore-studio-ui';
import { serviceIds } from 'pimcore-studio-ui/app';
import { type IconLibrary } from 'pimcore-studio-ui/modules/icon-library';
import { CoreShopIcon } from "../icons/coreshop";

export function registerIcons() {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary);
    iconLibrary.register({
        name: 'coreshop-icon',
        component: CoreShopIcon
    });
}
