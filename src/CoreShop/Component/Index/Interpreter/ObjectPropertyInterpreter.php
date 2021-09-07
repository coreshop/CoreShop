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
        array $interpreterConfig = []
    ): mixed
    {
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
