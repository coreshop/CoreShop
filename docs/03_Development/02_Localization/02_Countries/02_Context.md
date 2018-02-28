# CoreShop Country Context

For CoreShop to determine the current country the visitor or customer comes from,
it uses a concept called context and context resolver.

## Context
| Name | Priority | Tag | Description|
|------|----------|-----|------------|
| [FixedCountryContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Address/Context/FixedCountryContext.php) | default | `coreshop.context.country` | Used for testing purposes |
| [CountryContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Address/Context/RequestBased/CountryContext.php) | default | `coreshop.context.country` | Check for a country within the country request resolver |
| [StoreAwareCountryContext](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Core/Context/Country/StoreAwareCountryContext.php) | default | `coreshop.context.country` | Check if current country is available in current store context |

## Resolver

| Name | Priority | Tag | Description|
|------|----------|-----|------------|
| [GeoLiteBasedRequestResolver](https://github.com/coreshop/CoreShop/blob/master/src/CoreShop/Component/Address/Context/RequestBased/GeoLiteBasedRequestResolver.php) | 10 | `coreshop.context.country.request_based.resolver` | This Resolver tries to determinate the users location by using the Geo Lite Database. |

These resolver takes care about finding the correct country for the current request.

## Create a Custom Resolver

A Country Context needs to implement the interface `CoreShop\Component\Address\Context\CountryContextInterface`.
This interface consists of one method called `getCountry` which returns a `CoreShop\Component\Address\Model\CountryInterface` or throws an `CoreShop\Component\Address\Context\CountryNotFoundException`.

To register your context, you need to use the tag: `coreshop.context.country` with an optional `priority` attribute.

## Create a Request based Resolver

CoreShop already implements Request based country context resolver.
So if your Context depends on the current request, you can create a custom RequestBased Resolver.
To do that, implement the interface `CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface`
with the method `findCountry`. This method either returns a country or null.

To register this resolver, use the tag: `coreshop.context.country.request_based.resolver` with an optional `priority` attribute.

## Example

We want to a CountryContext to be based on the Pimcore Document. So if we are on site `/de`, we want to resolve to `Austria`, if we
are on page `/en` we want to resolve to Country `Great Britain`:

**1**: First of all we need to create our RequestBased Country Context:

```php
<?php

namespace AppBundle\CoreShop\Address\Context;

use CoreShop\Component\Address\Context\RequestBased\RequestResolverInterface;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;
use Pimcore\Http\Request\Resolver\DocumentResolver;
use Symfony\Component\HttpFoundation\Request;

final class DocumentBasedRequestRequestResolver implements RequestResolverInterface
{
    /**
     * @var DocumentResolver
     */
    private $pimcoreDocumentResolver;

    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    /**
     * @param DocumentResolver $pimcoreDocumentResolver
     * @param CountryRepositoryInterface $countryRepository
     */
    public function __construct(DocumentResolver $pimcoreDocumentResolver, CountryRepositoryInterface $countryRepository)
    {
        $this->pimcoreDocumentResolver = $pimcoreDocumentResolver;
        $this->countryRepository = $countryRepository;
    }

    public function findCountry(Request $request)
    {
        $doc = $this->pimcoreDocumentResolver->getDocument($request);

        if (substr($doc->getFullPath(), 0, 3) === '/en') {
            return $this->countryRepository->findByCode('GB');
        }

        if (substr($doc->getFullPath(), 0, 3) === '/de') {
            return $this->countryRepository->findByCode('AT');
        }

        return null;
    }
}
```

Now we need to configure the service in `src/AppBundle/Resources/config/services.yml`

```yaml
services:
  app.coreshop.country.context.request.document_based:
    class: AppBundle\CoreShop\Address\Context\DocumentBasedRequestRequestResolver
    arguments:
      - '@Pimcore\Http\Request\Resolver\DocumentResolver'
      - '@coreshop.repository.country'
    tags:
      - { name: coreshop.context.country.request_based.resolver }

```

CoreShop now tries to resolve the current country based on the Pimcore Site we are on.
