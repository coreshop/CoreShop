<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Index\Listing;

interface ExtendedListingInterface extends ListingInterface
{
    /**
     * loads group by values based on relation fieldname either from local variable if prepared or directly from product index.
     *
     * @param string $fieldName
     * @param string $type
     * @param bool   $countValues
     * @param bool   $fieldNameShouldBeExcluded => set to false for and-conditions
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getGroupByRelationValuesAndType(
        $fieldName,
        $type,
        $countValues = false,
        $fieldNameShouldBeExcluded = true
    );
}
