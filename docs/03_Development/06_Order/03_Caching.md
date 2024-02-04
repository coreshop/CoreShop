# Caching

Pimcore faces challenges with concurrency and caching, especially when handling DataObjects. The caching of DataObjects
in Pimcore occurs post-request during PHP termination. This can lead to delays if, for instance, complex Order Objects
are involved or if there are issues in the cache marshalling process. Subsequently, a new request may access an outdated
cached version of the Order Object, potentially causing inconsistencies.

To mitigate these issues, CoreShop provides a specific configuration option to disable caching for Storage List and
Storage List Item Objects:

```yaml
core_shop_storage_list:
  list:
    order:
      disable_caching: true
    wishlist:
      disable_caching: true
```
