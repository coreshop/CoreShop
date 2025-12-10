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

namespace CoreShop\Bundle\MessengerBundle\Stamp;

use Symfony\Component\Messenger\Stamp\StampInterface;

class PimcoreObjectStamp implements StampInterface
{
    public function __construct(
        private int $objectId,
    ) {
    }

    public function getObjectId(): int
    {
        return $this->objectId;
    }
}
