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

namespace CoreShop\Behat\Service;

use CoreShop\Component\Index\Interpreter\RelationalValue;
use CoreShop\Component\Index\Interpreter\RelationInterpreterInterface;
use CoreShop\Component\Index\Model\IndexableInterface;
use CoreShop\Component\Index\Model\IndexColumnInterface;

class CustomRelationalIndexInterpreter implements RelationInterpreterInterface
{
    public function interpret(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = []
    ): mixed {
        return $value;
    }

    public function interpretRelational(
        mixed $value,
        IndexableInterface $indexable,
        IndexColumnInterface $config,
        array $interpreterConfig = []
    ): array {
        return [
            new RelationalValue($indexable->getId(), 'test', ['custom_col' => 'blub'])
        ];
    }

    public function getRelationalColumns(): array
    {
        return [
            'custom_col' => IndexColumnInterface::FIELD_TYPE_STRING,
        ];
    }
}
