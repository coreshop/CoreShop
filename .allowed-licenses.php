<?php

declare(strict_types=1);

use Lendable\ComposerLicenseChecker\LicenseConfigurationBuilder;

return (new LicenseConfigurationBuilder())
    ->addLicenses(
        'MIT',
        'BSD-2-Clause',
        'BSD-3-Clause',
        'Apache-2.0',
        'OSL-3.0',
        'Artistic-1.0',
        'ISC',

    )
    ->addAllowedPackage('pimcore/admin-ui-classic-bundle')
    ->addAllowedPackage('pimcore/google-marketing-bundle')
    ->addAllowedPackage('pimcore/newsletter-bundle')
    ->addAllowedPackage('pimcore/opensearch-client')
    ->addAllowedPackage('pimcore/pimcore')
    ->build();