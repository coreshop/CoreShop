# CoreShop Index Bundle

Index Bundle is completely decoupled from CoreShop. It allows you to create Object-Indexes and Query them for faster results. It also adds Filters,
which allow you to create dynamic Frontend Filters for usability enhancements.

    - Symfony Forms
    - Index Types
        - Mysql
    - Pimcore Core Extensions
    - Filter Processors
    - Getter Implementation
    - Interpreter Implementation
    - Index Worker
    - Listing Services


How to get a Listing from an Index?

```php
$filter = $this->get('coreshop.repository.filter')->find(1); //Get Filter by ID 1
$filteredList = $this->get('coreshop.factory.filter.list')->createList($filter, $request->request);
$filteredList->setVariantMode(ListingInterface::VARIANT_MODE_HIDE);
$filteredList->setCategory($category);
$filteredList->load();
```