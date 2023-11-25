# Bundles

CoreShop solves a lot of non e-commerce related problems. Therefore, we have a lot of bundles which are not directly related to e-commerce and can be used in any Pimcore Installation.

- [Class Definition Patch](./ClassDefinitionPatch_Bundle.md)
- [Index Bundle](./Index_Bundle.md)
- [Money Bundle](./Money_Bundle.md)
- [Messenger Bundle](./Messenger_Bundle.md)
- [Optimistic Entity Lock Bundle](./OptimisticEntityLock_Bundle.md)
- [Resource Bundle](./Resource_Bundle)
- [SEO Bundle](./SEO_Bundle.md)
- [Sequence Bundle](./Sequence_Bundle.md)
- [Storage List Bundle](./StorageList_Bundle.md)
- [Theme Bundle](./Theme_Bundle.md)
- [Variant Bundle](./Variant_Bundle.md)

## Resource Bundle
Since CoreShop's major goal is to provide a flexible framework, we have a base-bundle that handles a lot of repetitive tasks for us, like:
 - Create, Read, Update, Delete Controllers (`ResourceController`)
 - It creates Factories, Repositories, Routes and Resource Controllers for CoreShop Resources

This is very powerful for Pimcore Bundles and thus can be used for any Pimcore Bundle. To find out more, read [this](./Resource_Bundle).
