<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Address\Repository;

use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Doctrine\ORM\QueryBuilder;

interface CountryRepositoryInterface extends RepositoryInterface
{
    /**
     * @return QueryBuilder
     */
    public function createListQueryBuilder();

    /**
     * @param string $name
     * @param string $locale
     *
     * @return CountryInterface[]
     */
    public function findByName($name, $locale);

    /**
     * @param string $code
     *
     * @return CountryInterface
     */
    public function findByCode($code);
}
