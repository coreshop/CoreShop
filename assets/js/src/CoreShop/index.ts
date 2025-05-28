import { type IAbstractPlugin } from '@pimcore/studio-ui-bundle';
import { CoreShopRegister } from './modules/coreShopRegister';
import { DynamicTypeExtension } from './modules/dynamicTypeExtension';
import { CoreShopRelationClass } from "./custom-types/coreShopRelation";
import {CoreShopStoreMultiselectClass} from "./custom-types/coreShopStoreMultiselect";
export const CoreShopPlugin: IAbstractPlugin = {
    name: 'CoreShopPlugin',
    onInit ({ container }) {
        container.bind('DynamicTypes/ObjectData/coreShopRelation').to(CoreShopRelationClass);
        container.bind('DynamicTypes/ObjectData/coreShopStoreMultiselect').to(CoreShopStoreMultiselectClass);
    },
    onStartup ({ moduleSystem }) {
        moduleSystem.registerModule(CoreShopRegister)
        moduleSystem.registerModule(DynamicTypeExtension)
    }
}