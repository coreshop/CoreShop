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
use Pimcore\Model\DataObject\Data\ObjectMetadata;

class ObjectInterpreter implements RelationInterpreterInterface
{
    public function interpretRelational(mixed $value, IndexableInterface $indexable, IndexColumnInterface $config, array $interpreterConfig = []): array
    {
        $result = [];

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof ObjectMetadata) {
                    $v = $v->getObject();
                }
                if ($v instanceof AbstractObject) {
                    $result[] = new RelationalValue($v->getId(), 'object');
                }
            }
        } elseif ($value instanceof AbstractObject) {
            $result[] = new RelationalValue($value->getId(), 'object');
        }

        return $result;
    }

    public function interpret(mixed $value, IndexableInterface $indexable, IndexColumnInterface $config, array $interpreterConfig = []): mixed
    {
        $result = [];

        if (is_array($value)) {
            foreach ($value as $v) {
                if ($v instanceof ObjectMetadata) {
                    $v = $v->getObject();
                }
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
