# CoreShop Store Context

For CoreShop to determine the current store the visitor or customer comes from
it uses a concept called context and context resolver.

## Context

| Name | Priority | Tag | Description |
|------|----------|-----|-------------|
| [FixedStoreContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/FixedStoreContext.php) | 2 | `coreshop.context.store ` | Used for testing purposes |
| [StoreContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/StoreContext.php) | 1 | `coreshop.context.store ` | Load a store from given request resolver |

## Resolver

| Name | Priority | Tag | Description |
|------|----------|-----|-------------|
| [SiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/SiteBasedRequestResolver.php) | 100 |  `coreshop.context.store.request_based.resolver` | Determines a store by a given pimcore frontend site |
| [PimcoreAdminSiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/PimcoreAdminSiteBasedRequestResolver.php) | 200 | `coreshop.context.store.request_based.resolver` | Determines a store by a given document in backend |

These resolver take care about finding the correct store for the current request.

## Create a Custom Resolver

A Store Context needs to implement the interface `CoreShop\Component\Store\Context\StoreContextInterface`.
This interface consists of one method called `getStore` which returns a `CoreShop\Component\Store\Model\StoreInterface` or throws an `CoreShop\Component\Store\Context\StoreNotFoundException`.

To register your context, you need to use the tag `coreshop.context.store` with an optional `priority` attribute.

## Create a Request based Resolver

CoreShop already implements Request based store context resolver. So if your context depends on the current request, you can
create a custom RequestBased resolver. To do that, implement the interface `CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface`
with the method `findStore`. This method either returns a store or null.

To register this resolver, use the tag: `coreshop.context.store.request_based.resolver` with an optional `priority` attribute.

## Example

We want to a StoreContext to be based on the Pimcore Document. So if we are on site `/de`, we want to resolve to `Store DE`, if we
are on page `/en` we want to resolve to Store `Store EN`:

**1**: First of all we need to create our RequestBased Store Context:

```php
<?php

namespace AppBundle\CoreShop\Store\Context;

use CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Store\Repository\StoreRepositoryInterface;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Symfony\Component\HttpFoundation\Request;

final class DocumentBasedRequestRequestResolver implements RequestResolverInterface
{
    private StoreRepositoryInterface $storeRepository;

    public function __construct(StoreRepositoryInterface $storeRepository)
    {
        $this->storeRepository = $storeRepository;
    }

    public function findStore(Request $request): ?StoreInterface
    {
        if (substr($request->getPathInfo(), 0, 3) === '/en') {
            return $this->storeRepository->find(1);
        }

        if (substr($request->getPathInfo(), 0, 3) === '/de') {
            return $this->storeRepository->find(2);
        }

        return null;
    }
}
```

Now we need to configure the service in `src/AppBundle/Resources/config/services.yml`

```yaml
services:
  app.coreshop.store.context.request.document_based:
    class: AppBundle\CoreShop\Store\Context\DocumentBasedRequestRequestResolver
    arguments:
      - '@coreshop.repository.store'
    tags:
      - { name: coreshop.context.store.request_based.resolver, priority: 300 }

```

CoreShop now tries to resolve the current Store based on the Pimcore Site we are on.
