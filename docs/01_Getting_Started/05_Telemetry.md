# Telemetry and License Check

CoreShop is licensed under the CoreShop Commercial License (CCL). To know which installations exist and whether they
carry a valid subscription, CoreShop sends a small, anonymous ping to the CoreShop license portal once every 24 hours.

The ping is sent by the Pimcore maintenance task `coreshop_telemetry`. If the maintenance is not running, it is also
triggered on demand by the `coreshop/enterprise-subscription-bundle` when the Pimcore Studio checks the subscription
status. It never runs inside a storefront request, uses a short timeout and never throws: a portal that is unreachable
only produces a warning in the application log.

## What is sent

| Field | Content |
|---|---|
| `id` | SHA-256 of the Pimcore instance identifier (`PIMCORE_INSTANCE_IDENTIFIER`). If none is configured, a UUID is generated once and stored in the Pimcore settings store. The raw identifier is never sent. |
| `pimcoreInstanceId` | HMAC of the Pimcore instance identifier with the Pimcore encryption secret, the same value Pimcore uses for its product registration. Omitted when either value is missing. |
| `hosts`, `currentDomain` | The main domain from the Pimcore system settings, all domains of all Pimcore sites, and the host of the current request if any. |
| `coreshop`, `pimcore`, `php` | Version numbers. |
| `environment` | The Symfony kernel environment (`prod`, `dev`, …). |
| `bundles` | Name and version of every installed `coreshop/*` Composer package and every package of type `pimcore-bundle`. |
| `production`, `tokenHash` | Contributed by the enterprise subscription bundle: whether the installation is flagged as a production system, and the SHA-256 of the subscription token. The token itself is never transmitted. |
| `timestamp` | Time of the ping. |

No customer data, no content, no order or product data is transmitted.

## Opt-out and configuration

```yaml
core_shop_core:
    telemetry:
        enabled: '%env(bool:CORESHOP_TELEMETRY)%'   # default true
        endpoint: 'https://license.coreshop.com/v1/ping'
        timeout: 4.0
```

Set `CORESHOP_TELEMETRY=false` in your `.env` to disable the ping entirely. Note that without the ping the license
portal cannot confirm your subscription and the Studio will show a warning.

## Manual ping

```bash
bin/console coreshop:telemetry:ping --dump   # show the payload without sending
bin/console coreshop:telemetry:ping          # send now and print the portal response
bin/console pimcore:maintenance --job=coreshop_telemetry
```

## Contributing data from a bundle

Implement `CoreShop\Component\Core\Telemetry\TelemetryDataProviderInterface` and return a partial payload. Services
implementing the interface are tagged `coreshop.telemetry.provider` automatically. Values are merged on top of the core
payload; `hosts` and `bundles` are concatenated.

```php
final class MyDataProvider implements TelemetryDataProviderInterface
{
    public function provide(): array
    {
        return ['myBundleFeatureX' => true];
    }
}
```
