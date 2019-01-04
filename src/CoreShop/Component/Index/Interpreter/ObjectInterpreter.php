<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Index\Interpreter;

use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;
use Pimcore\Model\DataObject\AbstractObject;

class ObjectInterpreter implements RelationInterpreterInterface
{
    /**
     * {@inheritdoc}
     */
    public function interpretRelational($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = [])
    {
        $result = [];

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof AbstractObject) {
                    $result[] = [
                        'dest' => $v->getId(),
                        'type' => 'object',
                    ];
                }
            }
        } elseif ($value instanceof AbstractObject) {
            $result[] = [
                'dest' => $value->getId(),
                'type' => 'object',
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function interpret($value, IndexableInterface $indexable, IndexColumnInterface $config, $interpreterConfig = [])
    {
        $result = [];

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof AbstractObject) {
                    $result[] = $v->getId();
                }
            }
        } elseif ($value instanceof AbstractObject) {
            $result[] = $value->getId();
        }

        return $result;
    }
}
