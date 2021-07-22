<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2021 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

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
    public function rename();

    /**
     * @return string
     */
    public function getOldFieldName();

    /**
     * @return string
     */
    public function getNewFieldName();

    /**
     * @return ClassDefinition|Fieldcollection\Definition|Objectbrick\Definition
     */
    public function getDefinition();
}
