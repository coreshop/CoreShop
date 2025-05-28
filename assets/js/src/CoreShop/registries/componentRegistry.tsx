import { container } from '@pimcore/studio-ui-bundle';
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import { type ComponentRegistry, componentConfig } from '@pimcore/studio-ui-bundle/modules/app';
import { CoreShopButton } from '../components/coreshop-button';

export function registerComponents() {
    const componentRegistry = container.get<ComponentRegistry>(serviceIds['App/ComponentRegistry/ComponentRegistry']);
    componentRegistry.registerToSlot(
        componentConfig.leftSidebar.slot.name,
        {
            name: 'coreShopButton',
            component: CoreShopButton,
            priority: 101
        }
    );
}
