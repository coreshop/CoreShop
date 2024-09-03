# Store Context

For CoreShop to determine the current store the visitor or customer comes from
it uses a concept called context and context resolver.

## Context

| Name                                                                                                                                | Priority | Tag                       | Description                              |
|-------------------------------------------------------------------------------------------------------------------------------------|----------|---------------------------|------------------------------------------|
| [FixedStoreContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/FixedStoreContext.php)    | 2        | `coreshop.context.store ` | Used for testing purposes                |
| [StoreContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/StoreContext.php) | 1        | `coreshop.context.store ` | Load a store from given request resolver |

## Resolver

| Name                                                                                                                                                                                | Priority | Tag                                             | Description                                         |
|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|-------------------------------------------------|-----------------------------------------------------|
| [SiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/SiteBasedRequestResolver.php)                         | 100      | `coreshop.context.store.request_based.resolver` | Determines a store by a given pimcore frontend site |
| [PimcoreAdminSiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/PimcoreAdminSiteBasedRequestResolver.php) | 200      | `coreshop.context.store.request_based.resolver` | Determines a store by a given document in backend   |

These resolver take care about finding the correct store for the current request.a# Store Context

CoreShop utilizes the concept of context and context resolver to determine the current store based on the visitor or
customer's location or preferences.

## Context

Contexts are used to identify the appropriate store for the current request:

| Name                                                                                                                                | Priority | Tag                       | Description                              |
|-------------------------------------------------------------------------------------------------------------------------------------|----------|---------------------------|------------------------------------------|
| [FixedStoreContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/FixedStoreContext.php)    | 2        | `coreshop.context.store ` | Used for testing purposes                |
| [StoreContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/StoreContext.php) | 1        | `coreshop.context.store ` | Load a store from given request resolver |

## Resolver

Resolvers are responsible for finding the correct store based on specific criteria:

| Name                                                                                                                                                                                | Priority | Tag                                             | Description                                         |
|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|----------|-------------------------------------------------|-----------------------------------------------------|
| [SiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/SiteBasedRequestResolver.php)                         | 100      | `coreshop.context.store.request_based.resolver` | Determines a store by a given Pimcore frontend site |
| [PimcoreAdminSiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/PimcoreAdminSiteBasedRequestResolver.php) | 200      | `coreshop.context.store.request_based.resolver` | Determines a store by a given document in backend   |

## Create a Custom Resolver

To create a custom Store Context:

1. Implement the interface `CoreShop\Component\Store\Context\StoreContextInterface`.
2. Define the `getStore` method to return a `CoreShop\Component\Store\Model\StoreInterface` or throw
   a `CoreShop\Component\Store\Context\StoreNotFoundException`.
3. Register your context using the tag `coreshop.context.store` with an optional `priority` attribute.

## Create a Request-Based Resolver

CoreShop supports request-based store context resolvers:

1. Implement the `CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface`.
2. Define the `findStore` method to return a store or null.
3. Register this resolver using the tag `coreshop.context.store.request_based.resolver` with an optional `priority`
   attribute.

## Example: Implementing a Document-Based Store Context

To resolve a Store based on a Pimcore document, such as differentiating between `/de` and `/en` sites:

1. Create the RequestBased Store Context:

```php
namespace App\CoreShop\Store\Context;

// ... other use statements ...

final class DocumentBasedRequestRequestResolver implements RequestResolverInterface
{
// Implementation details...
}
```

2. Configure the service in `config/services.yaml`:

```yaml
services:
   App\CoreShop\Store\Context\DocumentBasedRequestRequestResolver:
    arguments:
      - '@coreshop.repository.store'
    tags:
      - { name: coreshop.context.store.request_based.resolver, priority: 300 }
```

With this setup, CoreShop will dynamically resolve the current store based on the site being accessed.

## Create a Custom Resolver

A Store Context needs to implement the interface `CoreShop\Component\Store\Context\StoreContextInterface`.
This interface consists of one method called `getStore` which returns a `CoreShop\Component\Store\Model\StoreInterface`
or throws an `CoreShop\Component\Store\Context\StoreNotFoundException`.

To register your context, you need to use the tag `coreshop.context.store` with an optional `priority` attribute.

## Create a Request based Resolver

CoreShop already implements Request based store context resolver. So if your context depends on the current request, you
can
create a custom RequestBased resolver. To do that, implement the
interface `CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface`
with the method `findStore`. This method either returns a store or null.

To register this resolver, use the tag: `coreshop.context.store.request_based.resolver` with an optional `priority`
attribute.

## Example

We want to a StoreContext to be based on the Pimcore Document. So if we are on site `/de`, we want to resolve
to `Store DE`, if we
are on page `/en` we want to resolve to Store `Store EN`:

**1**: First of all we need to create our RequestBased Store Context:

```php
<?php

namespace App\CoreShop\Store\Context;

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

Now we need to configure the service in `config/services.yaml`

```yaml
services:
  App\CoreShop\Store\Context\DocumentBasedRequestRequestResolver:
    arguments:
      - '@coreshop.repository.store'
    tags:
      - { name: coreshop.context.store.request_based.resolver, priority: 300 }

```

CoreShop now tries to resolve the current Store based on the Pimcore Site we are on.
