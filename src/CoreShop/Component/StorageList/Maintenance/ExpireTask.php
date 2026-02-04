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
