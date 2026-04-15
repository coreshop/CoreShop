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
        $fieldNameShouldBeExcluded = true,
    );
}
