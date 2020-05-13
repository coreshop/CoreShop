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

use Pimcore\Model\DataObject\AbstractObject;
use Pimcore\Model\DataObject\Concrete;

interface ObjectClonerInterface
{
    /**
     * Clones an object and returns it unsaved.
     *
     * @param Concrete       $object
     * @param AbstractObject $parent
     * @param string         $key
     * @param bool           $saveDirectly
     *
     * @return Concrete
     */
    public function cloneObject(Concrete $object, AbstractObject $parent, string $key, bool $saveDirectly = true): Concrete;
}
