# Setup States Command

CoreShop provides a CLI command to create states/regions for countries after the initial installation.

By default, CoreShop only creates states for Austria (AT) during installation. Use this command to add states for additional countries.

## Usage

```bash
# Setup states for a single country
php bin/console coreshop:setup:states DE

# Setup states for multiple countries
php bin/console coreshop:setup:states DE,US,FR

# Setup states and activate the country if not already active
php bin/console coreshop:setup:states DE --activate-country

# Verbose output to see individual states being created
php bin/console coreshop:setup:states DE -v
```

## Arguments

| Argument | Description |
|----------|-------------|
| countries | Comma-separated list of country ISO codes (e.g., DE,US,FR) |

## Options

| Option | Description |
|--------|-------------|
| --activate-country | Also activate the country if not already active |

## Prerequisites

Before running this command, ensure that:

1. CoreShop is installed (`coreshop:install` has been run)
2. The country fixtures have been loaded (countries exist in the database)

## How It Works

The command:

1. Looks up each specified country in the database by ISO code
2. Loads the country's divisions (states/regions) from the Rinvex country data library
3. Creates states for each division that doesn't already exist
4. Optionally activates the country if `--activate-country` is specified

## Example Output

```
CoreShop States Setup
=====================

Setting up states for countries: DE

Processing country: DE
-----------------------

 [OK] Countries processed: 1
      States created: 16
      States skipped (already exist): 0
      Countries activated: 0
```
