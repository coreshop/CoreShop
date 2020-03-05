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

declare(strict_types=1);

namespace CoreShop\Component\Resource\Repository;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Listing;
use Pimcore\Model\Listing\AbstractListing;

interface PimcoreRepositoryInterface extends RepositoryInterface
{
    public function getClassId(): string;

    public function getList(): AbstractListing;

    public function forceFind(int $id, bool $force = true): ?Concrete;
}
