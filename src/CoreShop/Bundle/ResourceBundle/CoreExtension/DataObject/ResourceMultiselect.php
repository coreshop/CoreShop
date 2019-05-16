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

namespace CoreShop\Bundle\ResourceBundle\CoreExtension\DataObject;

use CoreShop\Component\Resource\Repository\RepositoryInterface;
use Pimcore\Model;

class ResourceMultiselect extends Model\DataObject\ClassDefinition\Data\Multiselect
{
    use DISetStateTrait;

    /**
     * @param string              $type
     * @param string              $model
     * @param RepositoryInterface $repository
     * @param array               $params
     */
    public function __construct(string $type, string $model, RepositoryInterface $repository, array $params = [])
    {
        $this->fieldtype = $type;
        $this->phpdocType = $model;
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
