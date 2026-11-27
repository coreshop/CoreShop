## 2026.3.0

### Pimcore 2026.3

CoreShop 2026.3 follows the Pimcore minor version and requires Pimcore 2026.3: `pimcore/pimcore`,
`pimcore/studio-ui-bundle`, `pimcore/studio-backend-bundle`, `pimcore/generic-data-index-bundle` and
`pimcore/opensearch-client` are now required as `^2026.3` (previously `^2026.2`). Update Pimcore to 2026.3 before
updating CoreShop. There are no other breaking changes compared to 2026.2.

### Country address formats are rendered in a Twig sandbox

The country address format is no longer rendered through the application's Twig environment but in a dedicated,
strict `Twig\Sandbox\Sandbox` (service `coreshop.address.formatter.sandbox`). Only a small allow-list of tags,
filters, tests and methods is available, and the new `AddressFormat` constraint rejects formats that use anything else
when a country is saved. CoreShop now requires `twig/twig` `^3.29` (#3304).

Existing address formats that use anything outside the allow-list (e.g. `raw` or custom Twig extensions) now throw a
`Twig\Sandbox\SecurityError` when rendered. See [Address Format](docs/03_Development/02_Localization/02_Countries/03_Address_Format.md)
and `UPGRADE-5.1.md`.

### Merged from 5.1.4

- All changes merged from 5.1.4, among them: Studio indexes no longer drop columns that share a class field (#3296)
  and submitted index columns are matched to the stored ones by name (#3299).

### Other changes

- Psalm: the doctrine/dbal 4.6 `Type` deprecations are reported as info (#3298).
- CI: upmerge pull requests are opened as coreshop-bot, and the 5.0 / 5.1 pull request workflows get the required-check
  gate (#3294, #3286).

