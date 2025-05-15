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
use Pimcore\Model\DataObject\AbstractObject;

class ObjectPropertyInterpreter implements InterpreterInterface
{
    public function interpret(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = [],
    ): mixed {
        if (($value instanceof AbstractObject) && array_key_exists('property', $interpreterConfig)) {
            $name = $interpreterConfig['property'];
            $getter = 'get' . ucfirst($name);

            if (method_exists($value, $getter)) {
                return $value->$getter();
            }
        }

        return null;
    }
}
