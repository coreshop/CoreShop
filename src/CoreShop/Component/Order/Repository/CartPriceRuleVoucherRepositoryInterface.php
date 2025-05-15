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

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface CartPriceRuleVoucherRepositoryInterface extends RepositoryInterface
{
    public function findAllPaginator(CartPriceRuleInterface $cartPriceRule, int $offset, int $limit): Paginator;

    public function findByCode(string $code): ?CartPriceRuleVoucherCodeInterface;

    public function countCodes(int $length, ?string $prefix = null, ?string $suffix = null): int;
}
