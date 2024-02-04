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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\StorageList\Maintenance;

use CoreShop\Component\StorageList\Expiration\StorageListExpirationInterface;
use Pimcore\Maintenance\TaskInterface;

final class ExpireTask implements TaskInterface
{
    public function __construct(
        private StorageListExpirationInterface $expirationService,
        private int $days = 0,
        private array $params = [],
    ) {
    }

    public function execute(): void
    {
        $this->expirationService->expire($this->days, $this->params);
    }
}
