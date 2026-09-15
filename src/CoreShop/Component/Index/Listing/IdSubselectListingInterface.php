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

/**
 * A listing that can express the ids of its matching rows as an SQL subselect, so other tables can be
 * restricted to the current listing set-based (e.g. price bounds of a range filter). SQL based workers only.
 */
interface IdSubselectListingInterface extends ListingInterface
{
    /**
     * SELECT statement returning one column with the object ids matching the listing's conditions,
     * without order, limit and offset. The condition registered under $excludedFieldName is left out,
     * the same way getGroupByValues() excludes the field being aggregated.
     */
    public function getIdSubselect(?string $excludedFieldName = null): string;
}
