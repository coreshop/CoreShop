<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2019 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Component\Pimcore\DataObject;

use Pimcore\Bundle\AdminBundle\Controller\Admin\DataObject\DataObjectController;
use Pimcore\Model\DataObject;

class EditmodeHelper
{
    private $objectData = [];
    private $metaData = [];

    public function getDataForObject(DataObject\Concrete $object, $objectFromVersion = false)
    {
        $this->objectData = [];

        foreach ($object->getClass()->getFieldDefinitions(['object' => $object]) as $key => $def) {
            $this->getDataForEditmode($object, $key, $def, $objectFromVersion);
        }

        return [
            'objectData' => $this->objectData,
            'metaData' => $this->metaData,
        ];
    }

    private function getDataForEditmode($object, $key, $fielddefinition, $objectFromVersion, $level = 0)
    {
        $dataObjectController = new DataObjectController();
        $trackerReflector = new \ReflectionClass(DataObjectController::class);
        $method = $trackerReflector->getMethod('getDataForField');
        $method->setAccessible(true);

        $method->getClosure($dataObjectController)($object, $key, $fielddefinition, $objectFromVersion, $level);

        $method->setAccessible(false);
    }
}
