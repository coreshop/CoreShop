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

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\Element\ElementInterface;


interface ObjectServiceInterface
{
    /**
     * @param $path
     *
     * @return ElementInterface
     */
    public function createFolderByPath($path);

    /**
     * Copy all fields from $from to $to.
     *
     * @param Concrete $fromObject
     * @param Concrete $toObject
     *
     * @return mixed
     */
    public function copyObject(Concrete $fromObject, Concrete $toObject);
}

\class_alias(ObjectServiceInterface::class, 'CoreShop\Component\Resource\Pimcore\ObjectServiceInterface');