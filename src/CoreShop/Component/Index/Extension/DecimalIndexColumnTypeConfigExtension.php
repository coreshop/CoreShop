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

namespace CoreShop\Component\Index\Extension;

use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

class DecimalIndexColumnTypeConfigExtension implements IndexColumnTypeConfigExtension
{
    public function getColumnConfig(IndexColumnInterface $column): array
    {
        if ($column->getColumnType() === IndexColumnInterface::FIELD_TYPE_DOUBLE) {
            return ['scale' => 2];
        }

        return [];
    }

    public function supports(IndexInterface $index): bool
    {
        return $index->getWorker() === 'mysql';
    }
}
