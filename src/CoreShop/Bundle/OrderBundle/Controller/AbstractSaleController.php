<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 */

namespace CoreShop\Bundle\OrderBundle\Controller;

use CoreShop\Bundle\ResourceBundle\Controller\PimcoreController;
use CoreShop\Component\Pimcore\DataObject\DataLoader;
use Pimcore\Model\DataObject\Concrete;

abstract class AbstractSaleController extends PimcoreController
{
    /**
     * @param mixed $data
     * @param array $loadedObjects
     *
     * @return array
     */
    protected function getDataForObject($data, $loadedObjects = [])
    {
        if ($data instanceof Concrete) {
            $dataLoader = new DataLoader();

            return $dataLoader->getDataForObject($data, $loadedObjects);
        }

        return [];
    }
}
