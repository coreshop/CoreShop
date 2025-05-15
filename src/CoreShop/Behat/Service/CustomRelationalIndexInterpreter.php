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

namespace CoreShop\Behat\Service;

use CoreShop\Component\Index\Interpreter\RelationalValue;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;

class CustomRelationalIndexInterpreter implements RelationInterpreterInterface
{
    public function interpret(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = [],
    ): mixed {
        return $value;
    }

    public function interpretRelational(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = [],
    ): array {
        return [
            new RelationalValue($indexable->getId(), 'test', ['custom_col' => 'blub']),
        ];
    }

    public function getRelationalColumns(): array
    {
        return [
            'custom_col' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }
}
