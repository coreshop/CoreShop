## 5.0.0

> CoreShop is now Licensed under CCL only! If you update to Version 5 make sure to read
> the [License](https://github.com/coreshop/CoreShop/blob/5.0/LICENSE.md) carefully and get in touch with us!

### Frontend Bundle

A new Frontend has been introduced. This could break existing Frontend implementations. Please make sure to copy the
old, not copied yet, Frontend Files to your implementation.

### Index Bundle

The IndexBundle Extensions (`IndexColumnsExtensionInterface`, `IndexRelationalColumnsExtensionInterface`) get*Columns
Methods for a MySQL Worker need to return a array of `Doctrine\DBAL\Schema\Column` now 