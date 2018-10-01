<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject;

interface ClassInstallerInterface
{
    /**
     * @param $jsonFile
     * @param $brickName
     * @return mixed|DataObject\Objectbrick\Definition
     */
    public function createBrick($jsonFile, $brickName);

    /**
     * @param $jsonFile
     * @param $className
     * @param bool $updateClass
     *
     * @return DataObject\ClassDefinition
     */
    public function createClass($jsonFile, $className, $updateClass = false);

    /**
     * @param $name
     * @param null $jsonFile
     *
     * @return mixed|null|DataObject\Fieldcollection\Definition
     */
    public function createFieldCollection($jsonFile, $name);
}