# CoreShop Store Context

For CoreShop to determine the current Store the visitor or customer comes from, it uses a concept called Context and Context Resolver.

CoreShop comes with a set of default Resolvers like:

 - [Pimcore Site Based](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/SiteBasedRequestResolver.php)
 - [Pimcore Admin Site Based](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Store/Context/RequestBased/PimcoreAdminSiteBasedRequestResolver.php)

These Resolver take care about finding the correct Store for the current Request.

## Create a Custom Resolver

A Store Context needs to implement the interface ```CoreShop\Component\Store\Context\StoreContextInterface```. This interface
consists of one function called "getStore" which returns a ```CoreShop\Component\Store\Model\StoreInterface``` or throws an ```CoreShop\Component\Store\Context\StoreNotFoundException```

To register your context, you need to use the tag: ```coreshop.context.store``` with an optional ```priority``` attribute.

## Create a Request based Resolver

CoreShop already implements Request based Store Context Resolver. So if your Context depends on the current request, you can
create a custom RequestBased Resolver. To do that, implement the interface ```CoreShop\Component\Store\Context\RequestBased\RequestResolverInterface```
with the function ```findStore```. This function either returns a Store or null.

To register this resolver, use the tag: ```coreshop.context.store.request_based.resolver``` with an optional ```priority``` attribute.

## Example

We want to a StoreContext to be based on the Pimcore Document. So if we are on site ```/de```, we want to resolve to ```Store DE```, if we
are on page ```/en``` we want to resolve to Store ```Store EN```:

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
    /**
     * @var DocumentResolver
     */
    private $pimcoreDocumentResolver;

    /**
     * @var StoreRepositoryInterface
     */
    private $storeRepository;

    /**
     * @param DocumentResolver $pimcoreDocumentResolver
     * @param StoreRepositoryInterface $storeRepository
     */
    public function __construct(DocumentResolver $pimcoreDocumentResolver, StoreRepositoryInterface $storeRepository)
    {
        $this->pimcoreDocumentResolver = $pimcoreDocumentResolver;
        $this->storeRepository = $storeRepository;
    }

    public function findStore(Request $request)
    {
        $doc = $this->pimcoreDocumentResolver->getDocument($request);

        if (substr($doc->getFullPath(), 0, 3) === '/en') {
            return $this->storeRepository->findById(1);
        }

        if (substr($doc->getFullPath(), 0, 3) === '/de') {
            return $this->storeRepository->findById(2);
        }

        return null;
    }
}
```

Now we need to configure the service in ```src/AppBundle/Resources/config/services.yml```

```yaml
services:
  app.coreshop.store.context.request.document_based:
    class: AppBundle\CoreShop\Store\Context\DocumentBasedRequestRequestResolver
    arguments:
      - '@Pimcore\Http\Request\Resolver\DocumentResolver'
      - '@coreshop.repository.store'
    tags:
      - { name: coreshop.context.store.request_based.resolver }

```

CoreShop now tries to resolve the current Store based on the Pimcore Site we are on.
