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

class LocalizedNestedInterpreter implements LocalizedInterpreterInterface
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
        throw new \Exception('method "interpret" in Localized Interpreter not allowed. Please use "interpretForLanguage" instead.');
    }

    public function interpretForLanguage(
        string $language,
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = [],
    ): mixed {
        $this->assert($interpreterConfig);

        return $this->loop($value, $interpreterConfig, static function (mixed $value, InterpreterInterface $interpreter, array $interpreterConfig) use ($language, $indexable, $config): mixed {
            if ($interpreter instanceof LocalizedInterpreterInterface) {
                return $interpreter->interpretForLanguage($language, $value, $indexable, $config, $interpreterConfig);
            }

            return $interpreter->interpret($value, $indexable, $config, $interpreterConfig);
        });
    }
}
