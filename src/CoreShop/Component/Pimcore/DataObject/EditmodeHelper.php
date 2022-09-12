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
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Bundle\AdminBundle\Controller\Admin\DataObject\DataObjectController;
use Pimcore\Model\DataObject;

class EditmodeHelper
{
    public function getDataForObject(DataObject\Concrete $object, bool $objectFromVersion = false): array
    {
        /** @psalm-suppress InternalClass */
        $dataObjectController = new DataObjectController();
        $trackerReflector = new \ReflectionClass(DataObjectController::class);
        $method = $trackerReflector->getMethod('getDataForObject');
        $method->setAccessible(true);
        $method->invoke($dataObjectController, $object, $objectFromVersion);
        $method->setAccessible(false);

        $objectData = $trackerReflector->getProperty('objectData');
        $metaData = $trackerReflector->getProperty('metaData');

        $objectData->setAccessible(true);
        $finalObjectData = $objectData->getValue($dataObjectController);
        $objectData->setAccessible(false);

        $metaData->setAccessible(true);
        $finalMetaData = $metaData->getValue($dataObjectController);
        $metaData->setAccessible(false);

        return [
            'objectData' => $finalObjectData,
            'metaData' => $finalMetaData,
        ];
    }
}
