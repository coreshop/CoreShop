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

namespace CoreShop\Component\Pimcore\DataObject\Grid;

use Pimcore\Model\DataObject;

interface GridFilterInterface
{
    /**
     * The name of filter action.
     * This value will be translated via backend translator,
     * so it's good practice to choose a symfony standard translation keys like "coreshop.grid.filter.your_filter_name".
     */
    public function getName(): string;

    public function filter(DataObject\Listing $list, array $context): DataObject\Listing;

    /**
     * Define if filter is valid for $type.
     */
    public function supports(string $listType): bool;
}
