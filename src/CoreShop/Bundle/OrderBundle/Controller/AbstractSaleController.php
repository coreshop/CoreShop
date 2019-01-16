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

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Pimcore\DataObject\DataLoader;
use Pimcore\Model\DataObject;

abstract class AbstractSaleController extends PimcoreController
{
    /**
     * @param DataObject\Concrete $data
     * @param array               $loadedObjects
     *
     * @return array
     */
    protected function getDataForObject(DataObject\Concrete $data, $loadedObjects = [])
    {
        $dataLoader = new DataLoader();

        return $dataLoader->getDataForObject($data, $loadedObjects);
    }
}
