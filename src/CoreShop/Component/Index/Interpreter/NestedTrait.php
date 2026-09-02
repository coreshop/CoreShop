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

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Registry\ServiceRegistryInterface;
use Webmozart\Assert\Assert;

trait NestedTrait
{
    protected ServiceRegistryInterface $interpreterRegistry;

    protected function loop(mixed $value, array $interpreterConfig, callable $callback): mixed
    {
        foreach ($interpreterConfig['interpreters'] as $interpreter) {
            $interpreterObject = $this->interpreterRegistry->get($interpreter['type']);

            $value = $callback($value, $interpreterObject, $interpreter['interpreterConfig']);
        }

        return $value;
    }

    protected function assert(array $interpreterConfig): void
    {
        Assert::keyExists($interpreterConfig, 'interpreters');
        Assert::isArray($interpreterConfig['interpreters'], 'Interpreter Config needs to be array');
    }
}
