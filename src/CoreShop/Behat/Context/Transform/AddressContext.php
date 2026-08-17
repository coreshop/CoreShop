<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under the terms of the
 * CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.com)
 * @license    CoreShop Commercial License (CCL)
 *
 */

namespace CoreShop\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use CoreShop\Bundle\TestBundle\Service\SharedStorageInterface;

final class AddressContext implements Context
{
    public function __construct(
        private SharedStorageInterface $sharedStorage,
    ) {
    }

    /**
     * @Transform /^address$/
     */
    public function address()
    {
        return $this->sharedStorage->get('address');
    }
}
