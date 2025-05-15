<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    https://www.coreshop.com/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Behat\Service\Tracking;

use CoreShop\Bundle\TrackingBundle\Resolver\ConfigResolverInterface;

class ConfigResolver implements ConfigResolverInterface
{
    public function __construct(
        private bool $addGtagCode = false,
    ) {
    }

    public function getGoogleConfig(): ?array
    {
        $params = [];

        if ($this->addGtagCode) {
            $params['gtagcode'] = 'coreshop';
        }

        return $params;
    }
}
