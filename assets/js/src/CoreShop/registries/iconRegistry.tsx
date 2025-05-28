import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import { type IconLibrary } from '@pimcore/studio-ui-bundle/modules/icon-library';
import { CoreShopIcon } from "../icons/coreshop";
import { CoreShopLogo } from "../icons/coreshopLogo";

export function registerIcons() {
    const iconLibrary = container.get<IconLibrary>(serviceIds.iconLibrary);
    iconLibrary.register({
        name: 'coreshop-icon',
        component: CoreShopIcon
    });
    iconLibrary.register({
        name: 'coreshop-logo',
        component: CoreShopLogo
    });
}
