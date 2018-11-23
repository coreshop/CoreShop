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
use Pimcore\Model\DataObject\Service;
use Webmozart\Assert\Assert;

class ObjectService implements ObjectServiceInterface
{
    /**
     * {@inheritdoc}
     */
    public function createFolderByPath($path)
    {
        return Service::createFolderByPath($path);
    }

    /**
     * {@inheritdoc}
     */
    public function copyObject(Concrete $fromObject, Concrete $toObject)
    {
        /**
         * @var $fromObject Concrete
         * @var $toObject   Concrete
         */
        Assert::isInstanceOf($fromObject, Concrete::class);
        Assert::isInstanceOf($toObject, Concrete::class);

        //load all in case of lazy loading fields
        $toFd = $toObject->getClass()->getFieldDefinitions();

        foreach ($toFd as $def) {
            $fromGetter = 'get' . ucfirst($def->getName());
            $toSetter = 'set' . ucfirst($def->getName());

            if (method_exists($fromObject, $fromGetter) && method_exists($toObject, $toSetter)) {
                $toObject->$toSetter($fromObject->$fromGetter());
            }
        }
    }
}
