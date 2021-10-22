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

namespace CoreShop\Behat\Service\Index;

use CoreShop\Component\Index\Extension\IndexRelationalColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

class RelationalIndexExtension implements IndexRelationalColumnsExtensionInterface
{
    public function supports(IndexInterface $index): bool
    {
        return $index->getName() === 'relational_extension';
    }

    public function getRelationalColumns(): array
    {
        return [
            'custom_col' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }
}
