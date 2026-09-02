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
    ->addAllowedVendor('pimcore')
    ->build();