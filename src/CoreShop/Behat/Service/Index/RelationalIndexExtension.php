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

namespace CoreShop\Behat\Service\Index;

use CoreShop\Component\Index\Extension\IndexRelationalColumnsExtensionInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;
use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class RelationalIndexExtension implements IndexRelationalColumnsExtensionInterface
{
    public function supports(IndexInterface $index): bool
    {
        return $index->getName() === 'relational_extension';
    }

    public function getRelationalColumns(): array
    {
        return [
            (new Column('custom_col', Type::getType('string')))->setLength(255),
        ];
    }
}
