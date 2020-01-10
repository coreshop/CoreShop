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

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Bundle\AdminBundle\Controller\Admin\DataObject\DataObjectController;
use Pimcore\Model\DataObject;

class EditmodeHelper
{
    public function getDataForObject(DataObject\Concrete $object, $objectFromVersion = false)
    {
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
