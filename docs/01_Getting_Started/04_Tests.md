# Tests

CoreShop comes with a full test-suite. We use [Behat](https://docs.behat.org/en/latest/) for our Domain-Tests and
Frontend Tests. It also comes with [Psalm](https://psalm.dev/) and [PHPStan](https://phpstan.org/) for Static Analysis.

## Running Tests Locally

### Psalm

```
vendor/bin/psalm
```

### PHPStan

```
SYMFONY_ENV=test vendor/bin/phpstan analyse -c phpstan.neon src -l 3 --memory-limit=-1
```

### BEHAT

- create database `coreshop5__behat`

#### Install Pimcore and CoreShop in Test Env

```
export PIMCORE_INSTALL_ENCRYPTION_SECRET=def000007849e770946460237224d11268f9eb0e29c019e3cf257d04fe1d332293d4329152e16e3be73a314d070ab9ba1672e912f237ca17606f7cb3c9894c85c61a3f6d
export PIMCORE_SECRET=def000007849e770946460237224d11268f9eb0e29c019e3cf257d04fe1d332293d4329152e16e3be73a314d070ab9ba1672e912f237ca17606f7cb3c9894c85c61a3f6d
export PIMCORE_INSTALL_INSTANCE_IDENTIFIER=4q7J2i3TzS5qnkSKK4hhir
export PIMCORE_INSTANCE_IDENTIFIER=4q7J2i3TzS5qnkSKK4hhir
export PIMCORE_PRODUCT_KEY=eyJwYXlsb2FkIjoie1wiaWRcIjpcImY2MTA3ZTAzMTA1YWUwNDdkMmU0YmQwMjY2NTMxNmViNDFlMDIwMTgyMDUyYmNjMGMwNjI1M2I0MjIwMDdlYWRcIixcInRpbWVzdGFtcFwiOjE3NDcxMzU4NTF9Iiwic2lnbmF0dXJlIjoiTUVVQ0lHWE5lQXhMSmVDeldBbDhzM1Jldjh3VHUwbUlGSEFreHJzcUxOSDlKN1M0QWlFQThYbXRVYjh0YkZRUllTVEU2ZmJmcHp5YVZtRlpIZW1aSldINlRPVkNXNHc9In0=
export PIMCORE_INSTALL_PRODUCT_KEY=eyJwYXlsb2FkIjoie1wiaWRcIjpcImY2MTA3ZTAzMTA1YWUwNDdkMmU0YmQwMjY2NTMxNmViNDFlMDIwMTgyMDUyYmNjMGMwNjI1M2I0MjIwMDdlYWRcIixcInRpbWVzdGFtcFwiOjE3NDcxMzU4NTF9Iiwic2lnbmF0dXJlIjoiTUVVQ0lHWE5lQXhMSmVDeldBbDhzM1Jldjh3VHUwbUlGSEFreHJzcUxOSDlKN1M0QWlFQThYbXRVYjh0YkZRUllTVEU2ZmJmcHp5YVZtRlpIZW1aSldINlRPVkNXNHc9In0=

APP_ENV=test PIMCORE_TEST_DB_DSN=mysql://root:ROOT@coreshop50-db/coreshop5_0___behat PIMCORE_INSTALL_ADMIN_USERNAME=admin PIMCORE_INSTALL_ADMIN_PASSWORD=admin PIMCORE_INSTALL_MYSQL_HOST_SOCKET=coreshop50-db PIMCORE_INSTALL_MYSQL_USERNAME=root PIMCORE_INSTALL_MYSQL_PASSWORD=ROOT PIMCORE_INSTALL_MYSQL_DATABASE=coreshop5_0___behat PIMCORE_INSTALL_MYSQL_PORT=3306 PIMCORE_KERNEL_CLASS=Kernel vendor/bin/pimcore-install --skip-product-registration-config --env=test --skip-database-config -n
APP_ENV=test PIMCORE_TEST_DB_DSN=mysql://root:ROOT@coreshop50-db/coreshop5_0___behat bin/console coreshop:install
```

#### BEHAT Domain

```
CORESHOP_SKIP_DB_SETUP=1 PIMCORE_TEST_DB_DSN=mysql://root:ROOT@coreshop50-db/coreshop5_0___behat vendor/bin/behat -c behat.yml.dist -p default
```

#### BEHAT UI

```
vendor/bin/bdi detect drivers

# OUTSIDE CONTAINER
# Run Symfony Server
APP_ENV=test PIMCORE_TEST_DB_DSN=mysql://root:ROOT@127.0.0.1:3306/coreshop5_0___behat symfony server:start --port=9080 --dir=public --no-tls

# Run Behat
CORESHOP_SKIP_DB_SETUP=1 PANTHER_EXTERNAL_BASE_URI=http://127.0.0.1:9080/index_test.php PANTHER_NO_HEADLESS=0 PIMCORE_TEST_DB_DSN=mysql://root:ROOT@127.0.0.1:3306/coreshop5_0___behat php -d memory_limit=-1 vendor/bin/behat -c behat.yml.dist -p ui -vvv features/ui/frontend/wishlist/adding_product_to_wishlist.feature 
```