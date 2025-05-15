import { type IAbstractPlugin } from 'pimcore-studio-ui';
import { CoreShopRegister } from './modules/coreshop-register';
import { DynamicTypeExtension } from './modules/dynamic-type-extension';
import { CoreShopRelationClass } from "./ObjectCustomTypes/coreShopRelation";
import {CoreShopStoreMultiselectClass} from "./ObjectCustomTypes/coreShopStoreMultiselect";
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