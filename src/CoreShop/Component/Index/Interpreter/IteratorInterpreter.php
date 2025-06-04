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
use Webmozart\Assert\Assert;

final class IteratorInterpreter implements InterpreterInterface
{
    public function __construct(
        private ServiceRegistryInterface $interpreterRegistry,
    ) {
    }

    public function interpret(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = [],
    ): mixed {
        Assert::isArray($value, 'IteratorInterpreter can only be used with array values');

        $interpreter = $interpreterConfig['interpreter'];
        /**
         * @var InterpreterInterface $interpreterObject
         */
        $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

        foreach ($value as &$val) {
            $val = $interpreterObject->interpret($val, $indexable, $config, $interpreter['interpreterConfig']);
        }

        return $value;
    }
}
