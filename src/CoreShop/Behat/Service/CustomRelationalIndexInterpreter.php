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

namespace CoreShop\Behat\Service;

use CoreShop\Component\Index\Extension\IndexRelationalColumnsExtensionInterface;
use CoreShop\Component\Index\Interpreter\RelationalValue;
use CoreShop\Component\Index\Interpreter\RelationalValueInterface;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Model\IndexInterface;

class CustomRelationalIndexInterpreter implements RelationInterpreterInterface
{
    public function interpret(
        $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        $interpreterConfig = []
    ) {
        return $value;
    }

    public function interpretRelational(
        $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        $interpreterConfig = []
    ) {
        return [
            new RelationalValue($indexable->getId(), 'test', ['custom_col' => 'blub'])
        ];
    }


    public function getRelationalColumns()
    {
        return [
            'custom_col' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }
}
