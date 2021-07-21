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

namespace CoreShop\Behat\Service\Index;

use CoreShop\Component\Index\Extension\IndexColumnTypeConfigExtension;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

class IndexColumnExtension implements IndexColumnTypeConfigExtension
{
    public function getColumnConfig(IndexColumnInterface $column): array
    {
        $config = [];

        if ($column->getColumnType() === IndexColumnInterface::FIELD_TYPE_DOUBLE) {
            return ['scale' => 20, 'precision' => 20];
        }

        return $config;
    }

    public function supports(IndexInterface $index): bool
    {
        return $index->getName() === 'extension_column_config';
    }
}
