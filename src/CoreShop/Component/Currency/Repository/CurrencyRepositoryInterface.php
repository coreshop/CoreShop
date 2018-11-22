<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Currency\Repository;

use CoreShop\Component\Currency\Model\CurrencyInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\QueryBuilder;

interface CurrencyRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @return CurrencyInterface[]
     */
    public function findActive();

    /**
     * @param string $currencyCode
     *
     * @return CurrencyInterface
     */
    public function getByCode($currencyCode);
}
