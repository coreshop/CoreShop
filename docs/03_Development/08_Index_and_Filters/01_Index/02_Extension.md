# Index Extension

To enhance the flexibility and functionality of the product index in CoreShop, you can create extensions. These
extensions allow for additional customization and optimization of the indexing process.

## Capabilities of Extensions

Extensions enable you to:

- Add additional "default" columns and their corresponding data to the index.
- Apply pre-filters to a MySQL index for more refined data retrieval.

## Implementing Extensions

To create an extension, implement one of the following interfaces depending on your needs:

- **Column Extensions**: Implement
  the [`CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Index/Extension/IndexColumnsExtensionInterface.php)
  if you want to add new columns to the index.
- **MySQL Query Extensions**: For extending MySQL query capabilities, use
  the [`CoreShop\Bundle\IndexBundle\Extension\MysqlIndexQueryExtensionInterface`](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Bundle/IndexBundle/Extension/MysqlIndexQueryExtensionInterface.php).

After implementing the appropriate interface, register your service with the tag `coreshop.index.extension`.

### Example of Creating an Extension

Here's a brief example to illustrate how you might implement and register an index extension:

```php
<?php

declare(strict_types=1);

namespace App\CoreShop\Index\Extension;

use CoreShop\Component\Core\Model\CategoryInterface;
use CoreShop\Component\Core\Model\ProductInterface;
use CoreShop\Component\Index\Extension\IndexColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

final class MyCustomExtension implements IndexColumnsExtensionInterface
{
    public function __construct(
        private string $productClassName,
    ) {
    }

    public function supports(IndexInterface $index): bool
    {
        return $this->productClassName === $index->getClass();
    }

    public function getSystemColumns(): array
    {
        return [
            'categoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'parentCategoryIds' => IndexColumnInterface::FIELD_TYPE_STRING,
            'stores' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }

    public function getLocalizedSystemColumns(): array
    {
        return [];
    }

    public function getIndexColumns(IndexableInterface $indexable): array
    {
        if ($indexable instanceof ProductInterface) {
            $categoryIds = [];
            $parentCategoryIds = [];

            $categories = $indexable->getCategories();
            $categories = is_array($categories) ? $categories : [];

            foreach ($categories as $c) {
                if ($c instanceof CategoryInterface) {
                    $categoryIds[$c->getId()] = $c->getId();

                    $parents = $c->getHierarchy();

                    foreach ($parents as $p) {
                        $parentCategoryIds[] = $p->getId();
                    }
                }
            }

            return [
                'categoryIds' => ',' . implode(',', $categoryIds) . ',',
                'parentCategoryIds' => ',' . implode(',', $parentCategoryIds) . ',',
                'stores' => ',' . @implode(',', $indexable->getStores()) . ',',
            ];
        }

        return [];
    }
}
```

And then register the extension in your service configuration:

```yaml
services:
  App\CoreShop\Index\Extension\MyCustomExtension:
    tags:
      - { name: coreshop.index.extension }
```

With these extensions, you can significantly enhance the capabilities of your product index, making it more adaptable to
your specific eCommerce needs.