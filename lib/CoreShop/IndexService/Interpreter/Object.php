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
 * @copyright  Copyright (c) 2015 Dominik Pfaffenbauer (http://dominik.pfaffenbauer.at)
 * @license    http://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */
namespace CoreShop\IndexService\Interpreter;

use CoreShop\Exception\UnsupportedException;
use Pimcore\Model\Object\AbstractObject;

class Object extends RelationInterpreter
{
    /**
     * interpret value.
     *
     * @param mixed $value
     * @param array $config
     *
     * @return mixed
     *
     * @throws UnsupportedException
     */
    public function interpret($value, $config = null)
    {
        $result = array();

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof AbstractObject) {
                    $result[] = array(
                        'dest' => $v->getId(),
                        'type' => 'object',
                    );
                }
            }
        } elseif ($value instanceof AbstractObject) {
            $result[] = array(
                'dest' => $value->getId(),
                'type' => 'object',
            );
        }

        return $result;
    }
}
