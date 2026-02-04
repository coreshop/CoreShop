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

namespace CoreShop\Component\Payment\Repository;

use CoreShop\Component\Payment\Model\PaymentProviderInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;

interface PaymentProviderRepositoryInterface extends RepositoryInterface
{
    /**
     * @return PaymentProviderInterface[]
     */
    public function findByTitle(string $title, string $locale): array;

    /**
     * @return PaymentProviderInterface[]
     */
    public function findActive(): array;
}
