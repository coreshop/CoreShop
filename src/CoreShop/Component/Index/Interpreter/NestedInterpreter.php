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

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use CoreShop\Component\Registry\ServiceRegistryInterface;

class NestedInterpreter implements InterpreterInterface
{
    use NestedTrait;

    public function __construct(
        ServiceRegistryInterface $interpreterRegistry,
    ) {
        $this->interpreterRegistry = $interpreterRegistry;
    }

    public function interpret(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = [],
    ): mixed {
        $this->assert($interpreterConfig);

        return $this->loop($value, $interpreterConfig, function (mixed $value, InterpreterInterface $interpreter, array $interpreterConfig) use ($indexable, $config): mixed {
            return $interpreter->interpret($value, $indexable, $config, $interpreterConfig);
        });
    }
}
