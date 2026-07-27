**CoreShop - Pimcore enhanced eCommerce**

![Static Tests (Lint, Stan)](https://github.com/coreshop/CoreShop/actions/workflows/static.yml/badge.svg)
[![Behat UI](https://github.com/coreshop/CoreShop/actions/workflows/behat_ui.yml/badge.svg)](https://github.com/coreshop/CoreShop/actions/workflows/behat_ui.yml)
[![Behat](https://github.com/coreshop/CoreShop/actions/workflows/behat.yml/badge.svg)](https://github.com/coreshop/CoreShop/actions/workflows/behat.yml)
[![Latest Pre-Release](https://img.shields.io/packagist/vpre/coreshop/core-shop.svg)](https://www.packagist.org/packages/coreshop/core-shop)

[![CoreShop](etc/illustration.png 'CoreShop')](https://www.coreshop.com)

CoreShop harnesses Pimcore's advanced features for unmatched efficiency and customization in your online store. Dive
into a seamless blend of cutting-edge technology and user-friendly interfaces, crafting an eCommerce experience that
transcends ordinary transactions.

# Further Information

- [Website](https://www.coreshop.com)
- [Documentation](https://docs.coreshop.com/latest)
- [Pimcore Github](https://github.com/pimcore/pimcore)

# Pimcore Studio UI

CoreShop 5.0 ships with a fully rewritten admin interface built on **Pimcore Studio v2** (React/TypeScript). Each CoreShop bundle provides its own Studio plugin using Module Federation, allowing independent development and deployment.

```bash
# Install frontend dependencies
npm ci

# Build all Studio plugins
npm run build

# Start dev server for a single bundle
npm run dev -- ResourceBundle
```

The build system auto-discovers all bundles with Studio assets under `src/CoreShop/Bundle/*/Resources/assets/pimcore-studio/` and builds them in parallel.

# Requirements

- Pimcore `^12.3`

# Installation

Read our Documentation for the Installation Guide [here](https://docs.coreshop.com/CoreShop/Getting_Started/Installation)

# Demo

Discover the full potential of CoreShop through our interactive demos. Read more about this [here](https://docs.coreshop.com/CoreShop/Getting_Started/Demo)

## Copyright and license

Copyright: [CoreShop GmbH](https://www.coreshop.com)
For licensing details please visit [LICENSE.md](LICENSE.md)
