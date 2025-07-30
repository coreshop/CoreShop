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

namespace CoreShop\Behat\Service;

use CoreShop\Component\Index\Interpreter\RelationalValue;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Index\Worker\MysqlWorkerInterface;

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
            'custom_col' => MysqlWorkerInterface::FIELD_TYPE_STRING,
        ];
    }
}
