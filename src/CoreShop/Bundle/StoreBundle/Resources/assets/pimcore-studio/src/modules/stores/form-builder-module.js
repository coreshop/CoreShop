import { container } from '@pimcore/studio-ui-bundle';
import { createStoreFormBuilder } from './StoreFormBuilder';
export const StoreFormBuilderModule = {
    onInit() {
        const builder = createStoreFormBuilder();
        container.bind('CoreShop/Store/Store/FormBuilder').toConstantValue(builder);
    }
};
