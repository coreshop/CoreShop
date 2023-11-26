# Caching

Pimcore has some issues with concurrency and caching. Pimcore is Caching the DataObjects after the Request has been sent
and PHP is terminating. This means if you have a complex Order Object, or somehting weird is going on in the Marshalling
of the Cache, it can take couple of seconds to create and store. In the meantime, another request might already come in
using the old cached verison of the Order Object. This can lead to some weird issues.

To control this, CoreShop has a special Configuration that allows you to disabling the caching completely for the
Storage List and Storage List Item Object:

```yaml
core_shop_storage_list:
    list:
        order:
          disable_caching: true
        wishlist:
          disable_caching: true
```
