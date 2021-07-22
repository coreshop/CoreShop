<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Order\Model\CartPriceRuleVoucherCodeInterface;
use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface CartPriceRuleVoucherRepositoryInterface extends RepositoryInterface
{
    public function findAllPaginator(CartPriceRuleInterface $cartPriceRule, int $offset, int $limit): Paginator;

    public function findByCode(string $code): ?CartPriceRuleVoucherCodeInterface;

    public function countCodes(int $length, ?string $prefix = null, ?string $suffix = null): int;
}
