import { type AbstractModule, container } from '@pimcore/studio-ui-bundle'
import { serviceIds } from '@pimcore/studio-ui-bundle/app';
import { type DynamicTypeObjectDataRegistry } from '@pimcore/studio-ui-bundle/modules/element';

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