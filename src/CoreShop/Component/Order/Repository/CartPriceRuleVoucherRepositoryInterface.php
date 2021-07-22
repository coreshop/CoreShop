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

namespace CoreShop\Component\Order\Repository;

use CoreShop\Component\Order\Model\CartPriceRuleInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;

interface CartPriceRuleVoucherRepositoryInterface extends RepositoryInterface
{
    /**
     * @param CartPriceRuleInterface $cartPriceRule
     * @param int $offset
     * @param int $limit
     *
     * @return Paginator
     */
    public function findAllPaginator(CartPriceRuleInterface $cartPriceRule, int $offset, int $limit);

    /**
     * @param string $code
     *
     * @return mixed
     */
    public function findByCode($code);

    /**
     * @param int    $length
     * @param string|null $prefix
     * @param string|null $suffix
     * @return int
     */
    public function countCodes(int $length, ?string $prefix = null, ?string $suffix = null);
}
