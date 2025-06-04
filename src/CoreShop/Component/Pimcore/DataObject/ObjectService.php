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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Folder;
use Pimcore\Model\DataObject\Service;

class ObjectService implements ObjectServiceInterface
{
    /**
     * @psalm-return Folder
     */
    public function createFolderByPath(string $path): Folder
    {
        /**
         * @var Folder $folder
         */
        $folder = Service::createFolderByPath($path);

        return $folder;
    }

    public function copyObject(Concrete $fromObject, Concrete $toObject): void
    {
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
