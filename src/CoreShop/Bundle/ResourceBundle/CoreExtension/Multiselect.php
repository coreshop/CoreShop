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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension;

use Pimcore\Model;

abstract class Multiselect extends Model\DataObject\ClassDefinition\Data\Multiselect
{
    /**
     * {@inheritdoc}
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * @param mixed $object
     * @param array $params
     *
     * @return mixed
     */
    public function preGetData($object, $params = [])
    {
        if (!$object instanceof Model\AbstractModel) {
            return null;
        }

        $data = $object->getObjectVar($this->getName());

        if (null === $data) {
            $data = [];
        }

        return $data;
    }
}
