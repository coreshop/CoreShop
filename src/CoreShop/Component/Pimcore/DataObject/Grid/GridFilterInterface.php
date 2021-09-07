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
