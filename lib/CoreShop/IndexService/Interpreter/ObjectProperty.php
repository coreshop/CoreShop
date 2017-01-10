<?php
/**
 * CoreShop.
 *
 * LICENSE
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\IndexService\Interpreter;

use CoreShop\Exception\UnsupportedException;
use CoreShop\Model\Index\Config\Column\AbstractColumn;
use Pimcore\Model\Object\AbstractObject;

/**
 * Class ObjectProperty
 * @package CoreShop\IndexService\Interpreter
 */
class ObjectProperty extends AbstractInterpreter
{
    /**
     * @var string
     */
    public static $type = 'objectProperty';

    /**
     * interpret value.
     *
     * @param mixed $value
     * @param AbstractColumn $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function interpret($value, $config = null)
    {
        $config = isset($config) ? $config->getInterpreterConfig() : [];

        if ($value instanceof AbstractObject) {
            if (array_key_exists("property", $config)) {
                $name = $config['property'];
                $getter = "get" . ucfirst($name);

                if (method_exists($value, $getter)) {
                    return $value->$getter();
                }
            }
        }

        return null;
    }
}
