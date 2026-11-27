# Address Format

Every country has an **Address Format**, a small Twig template that controls how addresses of that country are
rendered (e.g. by the `coreshop_format_address` Twig filter in checkout, order and invoice templates).

```twig
{{ company }}
{{ salutation }} {{ firstname }} {{ lastname }}
{{ street }} {{ number }}
{{ postcode }} {{ city }}
{{ country.name }}
```

All fields of the address are available as variables, plus `country` (the address's country object). The salutation is
already translated.

## Sandbox

Because the address format can be edited by back-office users, it is **never** rendered through the application's Twig
environment. `CoreShop\Component\Address\Formatter\AddressFormatSandboxFactory` creates a `Twig\Sandbox\Sandbox`
(service `coreshop.address.formatter.sandbox`) on a dedicated environment with a strict allow-list policy:

| Kind | Allowed |
|---|---|
| Tags | `if`, `for`, `set`, `apply` |
| Filters | `abs`, `capitalize`, `default`, `e`, `escape`, `first`, `format`, `join`, `last`, `length`, `lower`, `nl2br`, `replace`, `slice`, `split`, `striptags`, `title`, `trans`, `trim`, `upper` |
| Functions | none |
| Tests | `defined`, `empty`, `null`, `none`, `same as`, `odd`, `even`, `divisible by`, `iterable` |
| Methods | `CountryInterface::getName/getIsoCode`, `StateInterface::getName/getIsoCode`, `AddressIdentifierInterface::getName` |

Output is HTML-escaped. Anything else (e.g. `map`, `filter`, `sort`, `raw`, `include`, `source()`, arbitrary method
calls) is rejected. The `AddressFormat` validation constraint checks the format when a country is saved, so a disallowed
format cannot be stored.

### Customizing the policy

To allow more, override the `coreshop.address.formatter.sandbox` service and pass your own (strict)
`Twig\Sandbox\SecurityPolicyInterface` as second argument to `AddressFormatSandboxFactory::create()`. Never pass the
application's `@twig` service to the `AddressFormatter`: a Twig environment is ignored and replaced by the default
sandbox.
