import { type AbstractModule, container } from 'pimcore-studio-ui'
import { serviceIds } from 'pimcore-studio-ui/app';
import { type DynamicTypeObjectDataRegistry } from 'pimcore-studio-ui/modules/element';

export const DynamicTypeExtension: AbstractModule = {
    onInit: (): void => {
        const objectDataRegistry = container.get<DynamicTypeObjectDataRegistry>(serviceIds['DynamicTypes/ObjectDataRegistry']);
        objectDataRegistry.registerDynamicType(
            container.get('DynamicTypes/ObjectData/coreShopRelation')
        );

        objectDataRegistry.registerDynamicType(
            container.get('DynamicTypes/ObjectData/coreShopStoreMultiselect')
        );
    }
}