<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2020 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

declare(strict_types=1);

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\ClassDefinition;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Objectbrick;

/**
 * @experimental Use with caution only, this is a new experimental feature
 */
interface DefinitionFieldReNamerInterface
{
    /**
     * Executes the renaming.
     */
    public function rename(): void;

    /**
     * @return string
     */
    public function getOldFieldName(): string;

    /**
     * @return string
     */
    public function getNewFieldName(): string;

    /**
     * @return ClassDefinition|Fieldcollection\Definition|Objectbrick\Definition
     */
    public function getDefinition();
}
